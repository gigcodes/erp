<?php


namespace App;


class Status extends ReadOnlyBase {

	protected $data =[
		'Not Started' => '1',
		'In Progress' => '2',
		'Closed' => '3',
		'Lost' => '4',		
	];

	protected $messagestatus =[
		'Reply Pending' => '1',
		'Approval Pending' => '2',
		'Approved' => '3',
		'Send' => '4',		
	];

}