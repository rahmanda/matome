<?php
use Illuminate\Routing\Controller;
use Dingo\Api\Routing\ControllerTrait;
use Matome\Storage\ServerRepository;

class SchedulersController extends Controller {
	use ControllerTrait;
	/**
   * Injects model
   * 
   * @param ServerRepository $creator
   */
  public function __construct() {
    $this->server = new ServerRepository();
  }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = JWTAuth::parseToken()->toUser();
		$item = $this->server->getByUser($user['id']);
		if(isset($item['schedule'])) {
			$item['schedule'] = $this->convertToHumanTime($item['schedule']);
		}
		
		return $item;
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function store()
	{
		$user = JWTAuth::parseToken()->toUser();
		$input = Input::all();
		$input['schedule'] = $this->convertToCronTime($input['schedule']);
		$input['userId'] = $user['id'];
		$input['jobname'] = 'rsync-'.$input['userId'];

		$server = $this->server->create($input);
		$server['schedule'] = $this->convertToHumanTime($server['schedule']);

		return $server;
	}

	/**
	 * Convert to cron time
	 * 
	 * @param  string $key
	 * @return string     
	 */
	public function convertToCronTime($key)
	{	
		$cronTime = '';

		if($key == 'daily') {
			$cronTime = '0 0 * * *';
		} else if($key == 'weekly') {
			$cronTime = '0 0 * * 0';
		} else if($key == 'monthly'){
			$cronTime = '0 0 1 * *';
		} else {
			$cronTime = '* * * * *';
		}

		return $cronTime;
	}

	/**
	 * Conver to human time
	 * @param  string $cronTime
	 * @return string          
	 */
	public function convertToHumanTime($cronTime)
	{
		$schedule = '';

		if($cronTime == '0 0 * * *') {
			$schedule = 'daily';
		} else if($cronTime == '0 0 * * 0') {
			$schedule = 'weekly';
		} else if($cronTime == '0 0 1 * *') {
			$schedule = 'monthly';
		} else {
			$schedule = 'minute-maid';
		}

		return $schedule;
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function showById($id)
	{
		$item = $this->server->find($id);
		
		if(isset($item['schedule'])) {
			$item['schedule'] = $this->convertToHumanTime($item['schedule']);
		}
		
		return $item;
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$user = JWTAuth::parseToken()->toUser();
		$input = Input::all();

		// if userId is set in input, check if
		// there is an attempt to update another
		// user preference
		if(isset($input['userId'])) {
			if($user['id'] != $input['userId']) {				
				return $this->response->errorForbidden();
			}
		}

		$input['schedule'] = $this->convertToCronTime($input['schedule']);
		$input['userId'] = $user['id'];
		$input['jobname'] = 'rsync-'.$input['userId'];

		$this->server->update($input, $id);

		$server = $this->server->find($id);
		$server['schedule'] = $this->convertToHumanTime($server['schedule']);

		return $server;
				
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		return $this->server->destroy($id);
	}

}
