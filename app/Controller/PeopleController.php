<?php
App::uses('AppController', 'Controller');
/**
 * People Controller
 * @property Person $Person
 * @property PaginatorComponent $Paginator
 */
class PeopleController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');
	/*
/*
 * authorization
 * 
 */
	public function isAuthorized($user) {
	    // user can logout, dashboard, progress, history, suggest
	    if (isset($user['role']) && $user['role'] === 'user' ){
	    	if( in_array( $this->request->action, array('progress', 'logout', 'history', 'dashboard','suggest', 'coverDetails', 'performanceDetails'))){
	    		return true;
	    	}
	    }

	    return parent::isAuthorized($user);
	}
/*
* beforeFilter
*/
    public function beforeFilter(){
        parent::beforeFilter();
        // Allow users to
        $this->Auth->allow('register', 'login');

        Security::setHash('md5');
    }

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Person->recursive = 0;
		$this->set('people', $this->Paginator->paginate());
    }

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Person->exists($id)) {
			throw new NotFoundException(__('Invalid person'));
		}
		$options = array('conditions' => array('Person.' . $this->Person->primaryKey => $id));
		$this->set('person', $this->Person->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Person->create();
			if ($this->Person->save($this->request->data)) {
				$this->Session->setFlash(__('The person has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The person could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Person->exists($id)) {
			throw new NotFoundException(__('Invalid person'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Person->save($this->request->data)) {
				$this->Session->setFlash(__('The person has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The person could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Person.' . $this->Person->primaryKey => $id));
			$this->request->data = $this->Person->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Person->id = $id;
		if (!$this->Person->exists()) {
			throw new NotFoundException(__('Invalid person'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Person->delete()) {
			$this->Session->setFlash(__('The person has been deleted.'));
		} else {
			$this->Session->setFlash(__('The person could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/*
 * login page
 */
    public function login() {
        $this->autoRender = false;

        // If it is a post request we can assume this is a local login request
	    if ($this->request->isPost()){
	        if ($this->Auth->login()){
	            $this->redirect($this->Auth->redirectUrl());
	        } else {
	            $this->Session->setFlash(__('Invalid Username or password. Try again.'));
	        }
	    } 

	    // When facebook login is used, facebook always returns $_GET['code'].
	    elseif($this->request->query('code')){

	        // User login successful
	        $fb_user = $this->Facebook->getUser();          # Returns facebook user_id
	        if ($fb_user){
	            $fb_user = $this->Facebook->api('/me');     # Returns user information
	            $picture = $this->Facebook->api('/me/picture?height=200&width=200&redirect=false');		# FB picture
	
	            // We will varify if a local user exists first
	            $local_user = $this->Person->find('first', array(
	                'conditions' => array('facebook' => $fb_user['id'])
	            ));

	            // If exists, we will log them in
	            if ($local_user){
	                $this->Auth->login($local_user['Person']);            # Manual Login

	                // update data after login.
	                $this->Person->updateAll(
	                	array(
			                'first_name'=> '\''.$fb_user['first_name'].'\'',
			                'last_name'=> '\''.$fb_user['last_name'].'\'',
			                'image'=> '\''.$picture['data']['url'].'\'',
						),
	                	array( 'id' => $fb_user['id'])
	                );

	                $this->redirect($this->Auth->redirect());
	            }

	            // Otherwise we ll add a new user (Registration)
	            else {
	                $data['Person'] = array(
	                    'username'      => $fb_user['id'],                               	# Normally Unique
	                    'facebook'		=> $fb_user['id'],
	                    'password'      => AuthComponent::password(uniqid(md5(mt_rand()))), # Set random password
	                    'first_name'	=> $fb_user['first_name'],
	                    'last_name'		>= $fb_user['last_name'],
	                    'role'          => 'user',
	                    'date_created'	=> date("Y-m-d H:i:s"),
	                    'image'			=> $picture['data']['url']
	                );

	                // You should change this part to include data validation
	                $this->Person->save($data, array('validate' => false));

	                // After registration we will redirect them back here so they will be logged in
	                $this->redirect(array('controller' => 'people', 'action' => 'login'), array('code' => true));
	            }
	        }

	        else{
	            // User login failed..
	            $this->Session->setFlash(__('Something wrong, cannot log in. Please try again!'));
	        }
	    }
    }
/*
 * log out
 */
    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

 /*
  * dashboard.ctp
  */
    public function dashboard(){
        $this->layout = 'question_bank';
        $this->set('title_for_layout',"Dashboard");

        $this->loadModel('Progress');
        $performance = array();
        $performance['physics'] = $this->Progress->overall($this->Session->read('Auth.User')['id'], 'physics');
        $this->set('performance', $performance);
    }

/**
 * history
 */
	public function history(){
		$this->layout = 'question_bank';
		$this->loadModel('Score');
		$result = $this->Score->getAllScores($this->Session->read('Auth.User')['id']);
		$this->set('scores', $result);
	}

/**
 * suggestion
 */
	public function suggestion(){
		$this->layout = 'question_bank';
	}

/**
 * progress
 */	
	public function progress(){
		$this->layout = 'question_bank';
		$this->loadModel('Progress');
		$progresses = $this->Progress->getProgresses($this->Session->read('Auth.User')['id']);
		$this->set('progresses', $progresses);
	}

/**
 * performance details
 */
	public function performanceDetails($subject){
		$this->loadModel('Progress');

		// set to view
		$this->set('subject', $subject);
	}
/**
 *	coverage details
 */	
	public function coverDetails($subject){

		// set to view
		$this->set('subject', $subject);

		$this->loadModel('Category');
		$categories = $this->Category->find('all');

		$this->set('categories', $categories);
	}
}