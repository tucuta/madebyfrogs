<?php 

/**
   Frog CMS - Content Management Simplified. <http://www.madebyfrog.com>
   Copyright (C) 2008 Philippe Archambault <philippe.archambault@gmail.com>

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
   Class LoginController

   Log a use in and out and send a mail with something on 
   if the user doesn't remember is password !!!

   Since  0.1
 */

class LoginController extends Controller
{
    function __construct()
    {
        AuthUser::load();
    }
    
    function index()
    {
        // already log in ?
        if (AuthUser::isLoggedIn()) {
            if (Flash::get('redirect') != null)
                redirect(Flash::get('redirect'));
            else
                redirect(get_url());
        }
        
        // show it!
        $this->display('login/login', array(
            'username' => Flash::get('username'),
            'redirect' => Flash::get('redirect')
        ));
    }
    
    function login()
    {
        // already log in ?
        if (AuthUser::isLoggedIn())
            if (Flash::get('redirect') != null)
                redirect(Flash::get('redirect'));
            else
                redirect(get_url());
        
        if (get_request_method() == 'POST') {
            $data = isset($_POST['login']) ? $_POST['login']: array('username' => '', 'password' => '');
            Flash::set('username', $data['username']);
        
            if (AuthUser::login($data['username'], $data['password'], isset($data['remember'])))
            {
                $this->_checkVersion();
                // redirect to defaut controller and action
                if ($data['redirect'] != null && $data['redirect'] != 'null')
                    redirect($data['redirect']);
                else
                    redirect(get_url());
            }
            else Flash::set('error', __('Login failed. Please check your login data and try again.'));
        }
        
        // not find or password is wrong
        redirect(get_url('login'));
        
    }
    
    function logout()
    {
        AuthUser::logout();
        redirect(get_url());
    }
    
    function forgot()
    {
        if (get_request_method() == 'POST')
            return $this->_sendPasswordTo($_POST['forgot']['email']);
        
        $this->display('login/forgot', array('email' => Flash::get('email')));
    }
    
    function _sendPasswordTo($email)
    {
        $user = User::findBy('email', $email);
        if ($user)
        {
            use_helper('Email');
            
            $new_pass = '12'.dechex(rand(100000000, 4294967295)).'K';
            $user->password = sha1($new_pass);
            $user->save();
            
            $email = new Email();
            $email->from('no-reply@madebyfrog.com', 'Frog CMS');
            $email->to($user->email);
            $email->subject('Your new password from Frog CMS');
            $email->message('username: '.$user->username."\npassword: ".$new_pass);
            $email->send();
            
            Flash::set('success', 'An email has been send with your new password!');
            redirect(get_url('login'));
        }
        else
        {
            Flash::set('email', $email);
            Flash::set('error', 'No user found!');
            redirect(get_url('login/forgot'));
        }
    }
    
    function _checkVersion()
    {
        $v = file_get_contents('http://www.madebyfrog.com/version/');
        if ($v > FROG_VERSION)
        {
            Flash::set('error', __('<b>Information!</b> New Frog version available (v. <b>:version</b>)! Visit <a href="http://www.madebyfrog.com/">http://www.madebyfrog.com/</a> to upgrade your version!',
                       array(':version' => $v )));
        }
    }
    
} // end LoginController class
