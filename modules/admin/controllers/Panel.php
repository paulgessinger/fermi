<?php

/**
 * Default Controller of AdminAgent. Handles login, logout.
 *
 * @author Paul Gessinger
 */

class Panel extends FermiController 
{

	/**
	 * Logout action. Creates a new, empty model and stores it in the session. Subsequently redirects to login.
	 */
	function logoutAction()
	{
		
		$user = Core::getModel('core:User') ;
		Session::setUser($user) ;
		
		Core::redirect('admin', 'panel', 'login') ;
		
	}
	
	/**
	 * Renders a login page, that enables the user to log in.
	 */
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
			Core::fireEvent('onBeforeLogin') ;
			
			if($user = Core::getModel('core:User')->find('name=?', array($post['username'])))
			{	
				if(Session::generateHash($user->salt.$post['password']) == $user->pass)
				{
					$user->loadRights() ;
					if($user->authorize('admin'))
					{
						Session::setUser($user) ;
						
						Core::fireEvent('onAfterLogin', array('user' => $user)) ;
						
						Core::redirect('admin', 'dashboard', 'index') ;
						
					}
					else
					{
						Response::bind('message', 'access_denied') ;
						Core::fireEvent('onAccessDenies') ;
					}
				}
				else
				{
					Response::bind('message', 'username_password_wrong') ;
					Core::fireEvent('onUnsuccessfullLogin') ;
				}
			}
			else
			{
				Response::bind('message', 'username_password_wrong') ;
				Core::fireEvent('onUnsuccessfullLogin') ;
			}
			
			
			
			
			
			
			unset($post['password']) ;
			unset($post['submit']) ;
			$form->importPost($post) ;
		}
		
		
		Response::bind('form', $form) ;
		
		Response::setRootTemplate('login.phtml') ;
		Response::render() ;
	}
	
	
	/**
	 * Shows an access denied message to users that have a session, but do not have permission to view the admin panel.
	 */
	function accessdeniedAction()
	{
		
		//Core::redirect('Index', 'Sites', 'index') ;
		
		Response::setRootTemplate('access_denied.phtml') ;
		Response::render() ;
	}
}