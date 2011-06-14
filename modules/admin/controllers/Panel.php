<?php

class Panel extends FermiController 
{
	function __construct() {}
	
	function logoutAction()
	{
		
		$user = Core::getModel('core:User') ;
		Session::setUser($user) ;
		
		Core::redirect('admin', 'panel', 'login') ;
		
	}
	
	function loginAction()
	{
		
		$form = new Form('login_form', Core::getUrl('admin', 'panel', 'login'), 'POST') ;
		
		$options = array(
			'label' => 'username_field_label',
		) ;
		$form->addElement('text', 'username', $options) ;
		
		
		$options = array(
			'label' => 'password_field_label',
		) ;
		$form->addElement('password', 'password', $options) ;
		
		
		$options = array(
			'value' => 'login_submit',
		) ;
		$form->addElement('submit', 'submit', $options) ;
		
		
		
		if($post = Request::getPost())
		{
			if($user = Core::getModel('core:User')->find('name=?', array($post['username'])))
			{	
				if(Session::generateHash($user->salt.$post['password']) == $user->pass)
				{
					$user->loadRights() ;
					if($user->authorize('admin'))
					{
						Session::setUser($user) ;
						
						Core::redirect('admin', 'dashboard', 'index') ;
					}
					else
					{
						Response::bind('message', 'access_denied') ;
					}
				}
				else
				{
					Response::bind('message', 'username_password_wrong') ;
				}
			}
			else
			{
				Response::bind('message', 'username_password_wrong') ;
			}
			
			
			
			
			
			
			unset($post['password']) ;
			$form->importPost($post) ;
		}
		
		
		Response::bind('form', $form) ;
		
		Response::setRootTemplate('login.phtml') ;
		Response::render() ;
	}
	
	function accessdeniedAction()
	{
		
		//Core::redirect('Index', 'Sites', 'index') ;
		
		Response::setRootTemplate('access_denied.phtml') ;
		Response::render() ;
	}
}