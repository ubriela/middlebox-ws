<?php 
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$config = array(
	'signup' => array(
		array(
			'field' => 'username',
			'label' => 'Username',
			'rules' => 'trim|required|max_length[30]|xss_clean|is_unique[users.username]|alpha_dash',	
		),
		array(
			'field' => 'password',
			'label' => 'Password',
			'rules' => 'trim|required|max_length[128]|xss_clean|alpha_dash'	
		),
		array(
			'field' => 'email',
			'label' => 'Email',
			'rules' => 'trim|required|valid_email|max_length[50]|xss_clean|is_unique[users.email]'
		),
	),
    'login_username' => array(
        array(
            'field' => 'username',
            'label' => 'Username',
            'rules' => 'trim|required|max_length[50]|xss_clean'
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'trim|required|max_length[128]|xss_clean|alpha_dash'
        )
    ),
	'login_email' => array(
		array(
			'field'	=> 'email',
			'label' => 'Email',
			'rules' => 'trim|required|valid_email|max_length[50]|xss_clean'
		),
		array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'trim|required|max_length[128]|xss_clean|alpha_dash'
        )
	),
	'set_avatar' => array(
		array(
			'field' => 'avatar',
			'label' => 'Avatar',
			'rules' => 'required|xss_clean|'	
		)		
	),
	'set_alias' => array(
		array(
			'field' => 'alias',
			'label' => 'Alias',
			'rules' => 'trim|required|xss_clean|max_length[50]'	
		)
	),
	'change_password' => array(
		array(
			'field' => 'oldpass',
			'label' => 'Old Password',
			'rules' => 'trim|required|max_length[128]|xss_clean|alpha_dash'
		),
		array(
			'field' => 'newpass',
			'label' => 'New Password',
			'rules' => 'trim|required|max_length[128]|xss_clean|alpha_dash'			
		)
	),
	'get_user' => array(
		array(
			'field' => 'userid',
			'label' => 'User ID',
			'rules' => 'required|integer|greater_than[0]'	
		),
	),
    'forget_password_email' => array(
        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'trim|required|valid_email|max_length[128]|xss_clean|'
        )
    ),
	'forget_password_username' => array(
		array(
			'field' => 'username',
			'label' => 'Username',
			'rules' => 'trim|required|max_length[128]|xss_clean|'
		)	
	),
    'reset_password' => array(
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'trim|required|max_length[128]|xss_clean|'
        ),
    ),
	'invite_friend' => array(
		array(
			'field' => 'inviteeid',
			'label' => 'Invitee ID',
			'rules' => 'required|integer|greater_than[0]'
		),
	),
	'accept_reject_friend' => array(
		array(
			'field' => 'inviterid',
			'label' => 'Inviter ID',
			'rules' => 'required|integer|greater_than[0]'	
		),
	),
	'unfriend' => array(
		array(
			'field' => 'friendid',
			'label' => 'Friend ID',
			'rules' => 'required|integer|greater_than[0]'			
		),
	),
	
	//Bills
	'create_bill' => array(
		array(
			'field' => 'billdesc',
			'label' => 'Bill description',
			'rules' => 'trim|required|max_length[100]|xss_clean'
		),
		array(
			'field' => 'amount',
			'label' => 'Amount',
			'rules' => 'trim|required|xss_clean|numeric|greater_than[0]'		
		),
		array(
			'field' => 'tip',
			'label' => 'Tip',
			'rules' => 'trim|required|xss_clean|numeric|greater_than_or_equal[0]'	
		),
	),
    'update_bill' => array(
		array(
			'field' => 'billid',
			'label' => 'Bill ID',
			'rules' => 'required|xss_clean|integer|greater_than[0]'	
		),
		array(
			'field' => 'billdesc',
			'label' => 'Bill description',
			'rules' => 'trim|max_length[100]|xss_clean'
		),
		array(
			'field' => 'amount',
			'label' => 'Amount',
			'rules' => 'xss_clean|numeric|greater_than[0]'		
		),
		array(
			'field' => 'tip',
			'label' => 'Tip',
			'rules' => 'xss_clean|numeric|greater_than[0]'	
		),
	),
	'delete_bill' => array(
		array(
			'field' => 'billid',
			'label' => 'Bill ID',
			'rules' => 'required|xss_clean|integer|greater_than[0]'	
		),
	),
	'finish_bill' => array(
		array(
			'field' => 'billid',
			'label' => 'Bill ID',
			'rules' => 'required|xss_clean|integer|greater_than[0]'	
		),
	),
	'fetch_bills' => array(
		array(
			'field' => 'last_sync_time',
			'label' => 'Last sync time',
			'rules' => "trim|xss_clean|valid_datetime"
			//'rules' => 'trim|xss_clean'	

		),
		array(
			'field' => 'isdone',
			'label' => 'Is done',
			'rules' => 'regex_match[/^[01]$/]'	
		)
	),
	// Email requests
	'create_email_request' => array(
		array(
			'field' => 'billid',
			'label' => 'Bill ID',
			'rules' => 'required|xss_clean|integer|greater_than[0]'	
		),
		array(
			'field' => 'email',
			'label' => 'Email',
			'rules' => 'trim|required|valid_email|max_length[50]|xss_clean'
		),
		array(
			'field' => 'fullname',
			'label' => 'Fullname',
			'rules' => "trim|required|max_length[50]|xss_clean|valid_fullname"
		)
	),
	'create_emails_request' => array(
		array(
			'field' => 'billid',
			'label' => 'Bill ID',
			'rules' => 'required|xss_clean|integer|greater_than[0]'	
		),
		array(
			'field' => 'emails',
			'label' => 'Emails',
			'rules' => 'trim|required|xss_clean'
		)
	),
	'update_email_request' => array(
		array(
			'field' => 'billid',
			'label' => 'Bill ID',
			'rules' => 'required|xss_clean|integer|greater_than[0]'	
		),
		array(
			'field' => 'email',
			'label' => 'Email',
			'rules' => 'trim|required|valid_email|max_length[50]|xss_clean'
		),
		array(
			'field' => 'fullname',
			'label' => 'Fullname',
			'rules' => "trim|required|max_length[50]|xss_clean|valid_fullname"
		)
	),
	'delete_email_request' => array(
		array(
			'field' => 'billid',
			'label' => 'Bill ID',
			'rules' => 'required|xss_clean|integer|greater_than[0]'	
		),
		array(
			'field' => 'email',
			'label' => 'Email',
			'rules' => 'trim|required|valid_email|max_length[50]|xss_clean'
		)
	),
	'delete_emails_request' => array(
		array(
			'field' => 'billid',
			'label' => 'Bill ID',
			'rules' => 'required|xss_clean|integer|greater_than[0]'	
		),
		array(
			'field' => 'emails',
			'label' => 'Emails',
			'rules' => 'trim|required|xss_clean'
		)
	),
	'confirm_email_request' => array(
		array(
			'field' => 'billid',
			'label' => 'Bill ID',
			'rules' => 'required|xss_clean|integer|greater_than[0]'	
		),
		array(
			'field' => 'email',
			'label' => 'Email',
			'rules' => 'trim|required|valid_email|max_length[50]|xss_clean'
		)
	),
	'confirm_emails_request' => array(
		array(
			'field' => 'billid',
			'label' => 'Bill ID',
			'rules' => 'required|xss_clean|integer|greater_than[0]'	
		),
		array(
			'field' => 'emails',
			'label' => 'Emails',
			'rules' => 'trim|required|xss_clean'
		)
	)
);

/* End of file form_validation.php */
/* Location: ./application/config/form_validation.php */