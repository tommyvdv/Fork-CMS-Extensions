<?php

/*
 * This file is part of the amazon_s3 module.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */
/**
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class BackendAmazonS3AjaxLoadBucketInfo extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{

		parent::execute();

		// get parameters
		$bucket = SpoonFilter::getPostValue('bucket', null, '');

		// check input
		if(empty($bucket)) $this->output(self::BAD_REQUEST);

		// get basic details for this client
		$url = BackendAmazonS3Helper::get()->get_object_url($bucket, '');
		$region = BackendAmazonS3Helper::get()->get_bucket_region($bucket);

		$data['bucket'] = $bucket;
		$data['url'] = $url;
		$data['region'] = $region->body;

		// CM was successfully initialized
		$this->output(self::OK, $data);
	}
}
