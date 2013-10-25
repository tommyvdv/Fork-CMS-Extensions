<?php

/*
 * This file is part of the photogallery module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
* This album overview action
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendPhotogalleryIndex extends BackendBaseActionIndex
{
	/**
	 * Filter variables
	 *
	 * @var	array
	 */
	private $filter;

	/**
	 * Form
	 *
	 * @var BackendForm
	 */
	private $frm;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();
				
		// set filter
		$this->setFilter();

		// load form
		$this->loadForm();

		// load dataGrids
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Builds the query for this dataGrid
	 *
	 * @return array An array with two arguments containing the query and its parameters.
	 */
	private function buildQuery()
	{
		// init var
		$parameters = array();

		// basic query
		$query = 'SELECT l.id, l.sequence, l.title, l.num_images, l.hidden as is_hidden, UNIX_TIMESTAMP(l.publish_on) as publish_on, l.num_images_not_hidden,
				GROUP_CONCAT(DISTINCT c.title SEPARATOR ", ") AS categories
			FROM photogallery_albums AS l
				LEFT JOIN photogallery_categories_albums AS ca ON ca.album_id = l.id
				LEFT JOIN photogallery_categories AS c ON c.id = ca.category_id
			WHERE 1';

		// add title
		if($this->filter['title'] !== null)
		{
			$query .= ' AND l.title LIKE ?';
			$parameters[] = '%' . $this->filter['title'] . '%';
		}

		// add category_id
		if($this->filter['hidden'] !== null)
		{
			$query .= ' AND l.hidden = ?';
			$parameters[] = $this->filter['hidden'];
		}

		$query .= ' AND l.language = ?';
		$parameters[] = BL::getWorkingLanguage();

		// grouping & sorting
		$query .= ' GROUP BY l.id';

		// query + parameters
		return array($query, $parameters);
	}

	/**
	 * Load the dataGrid
	 */
	private function loadDataGrid()
	{
		// fetch query and parameters
		list($query, $parameters) = $this->buildQuery();

		// create dataGrid
		$this->dataGrid = new BackendDataGridDB($query, $parameters);
		$this->dataGrid->setMassActionCheckboxes('checkbox', '[id]');
		$this->dataGrid->setSortParameter('desc');

		$this->dataGrid->enableSequenceByDragAndDrop();

		// sorting columns
		$this->dataGrid->setSortingColumns(array('title','publish_on', 'sequence'), 'sequence');

		// set colum URLs
		$this->dataGrid->setColumnURL('title', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

		// add columns
		$this->dataGrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::getLabel('Edit'));
		$this->dataGrid->addColumn('preview', SpoonFilter::ucfirst(BL::lbl('Preview')));

		// Hide
		$this->dataGrid->setColumnHidden('is_hidden');
		$this->dataGrid->setColumnHidden('num_images_not_hidden');

		$this->dataGrid->setHeaderLabels(array('num_images' => '&nbsp;'));

		$this->dataGrid->setColumnFunction(array('BackendDatagridFunctions', 'getLongDate'), array('[publish_on]'), 'publish_on', true);
		$this->dataGrid->setColumnFunction(create_function('$is_hidden','return $is_hidden = $is_hidden == "Y" ? SpoonFilter::ucfirst(Bl::getLabel("Yes")) : SpoonFilter::ucfirst(Bl::getLabel("No"));'),array('[is_hidden]'),'is_hidden',true);
		$this->dataGrid->setColumnFunction(array('BackendPhotogalleryHelper', 'getPreviewHTMLForAlbums50x50_crop'), array('[id]', $this->getModule(),), 'preview', true);
		$this->dataGrid->setColumnFunction(array('BackendPhotogalleryHelper', 'getNumImagesForAlbums'), array('[num_images_not_hidden]', '[num_images]'), 'num_images', true);

		$this->dataGrid->setColumnAttributes('num_images', array('class' => 'small'));

		$this->dataGrid->setColumnsSequence(array('dragAndDropHandle','checkbox','preview','num_images','title','publish_on','is_hidden','edit'));

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('delete' => BL::getLabel('Delete'), 'hide' => BL::getLabel('Hide'), 'publish' => BL::getLabel('Publish')), 'delete');
		$ddmMassAction->setAttribute('id', 'actionDelete');
		$ddmMassAction->setOptionAttributes('delete', array('data-message-id' => 'confirmDelete')); // was rel
		$ddmMassAction->setOptionAttributes('hidden', array('data-message-id' => 'confirmHidden'));
		$ddmMassAction->setOptionAttributes('published', array('data-message-id' => 'confirmPublished'));
		$this->dataGrid->setMassAction($ddmMassAction);
		$this->dataGrid->setAttributes(array('data-action' => "sequence"));


	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('filter', BackendModel::createURLForAction(), 'get');

		// add fields
		$this->frm->addText('title', $this->filter['title'])->setAttributes(array('id' => 'noMetaTitle'));
		$this->frm->addDropdown('hidden', array('Y' => BL::getLabel('Hidden'), 'N' => BL::getLabel('Published')), $this->filter['hidden']);

		$this->frm->getField('hidden')->setdefaultelement(SpoonFilter::ucfirst(BL::getLabel('ChooseAStatus')));

		// manually parse fields
		$this->frm->parse($this->tpl);
	}

	/**
	 * Parse & display the page
	 */
	protected function parse()
	{
		// parse dataGrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);

		// parse paging & sorting
		$this->tpl->assign('offset', (int) $this->dataGrid->getOffset());
		$this->tpl->assign('order', (string) $this->dataGrid->getOrder());
		$this->tpl->assign('sort', (string) $this->dataGrid->getSort());

		// is filtered?
		if($this->getParameter('form', 'string', '') == 'filter') $this->tpl->assign('filter', true);

		// parse filter
		$this->tpl->assign($this->filter);
	}

	/**
	 * Sets the filter based on the $_GET array.
	 */
	private function setFilter()
	{
		$this->filter['title'] = $this->getParameter('title');
		$this->filter['hidden'] = $this->getParameter('hidden');
	}
}