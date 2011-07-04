<?php

/**
 * This saved the date on which the mailing is to be sent
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorAjaxSaveSendDate extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$mailingId = SpoonFilter::getPostValue('mailing_id', null, '', 'int');
		$sendOnDate = SpoonFilter::getPostValue('send_on_date', null, BackendModel::getUTCDate('d/m/Y'));
		$sendOnTime = SpoonFilter::getPostValue('send_on_time', null, BackendModel::getUTCDate('H:i'));
		$messageDate = $sendOnDate;

		// validate mailing ID
		if($mailingId == '') $this->output(self::BAD_REQUEST, null, 'Provide a valid mailing ID');
		if($sendOnDate == '' || $sendOnTime == '') $this->output(self::BAD_REQUEST, null, 'Provide a valid send date date provided');

		// record is empty
		if(!BackendMailmotorModel::existsMailing($mailingId)) $this->output(self::BAD_REQUEST, null, BL::err('MailingDoesNotExist', 'mailmotor'));

		// reverse the date and make it a proper
		$explodedDate = explode('/', $sendOnDate);
		$sendOnDate = $explodedDate[2] . '-' . $explodedDate[1] . '-' . $explodedDate[0];

		// calc full send timestamp
		$sendTimestamp = strtotime($sendOnDate . ' ' . $sendOnTime);

		// build data
		$item['id'] = $mailingId;
		$item['send_on'] = BackendModel::getUTCDate('Y-m-d H:i:s', $sendTimestamp);
		$item['edited_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');

		// update mailing
		BackendMailmotorModel::updateMailing($item);

		// trigger event
		BackendModel::triggerEvent($this->getModule(), 'after_edit_mailing_step4', array('item' => $item));

		// output
		$this->output(self::OK, array('mailing_id' => $mailingId, 'timestamp' => $sendTimestamp), sprintf(BL::msg('SendOn', 'mailmotor'), $messageDate, $sendOnTime));
	}
}

?>