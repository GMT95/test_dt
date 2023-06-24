<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * BookingController constructor.
     * @param BookingRepository $repository
     */
    public function __construct(protected BookingRepository $repository)
    {
    }
    /* Use php 8 new constructor syntax */

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        try {

            $adminIds = [env('ADMIN_ROLE_ID'), env('SUPERADMIN_ROLE_ID')];

            if(in_array($request->__authenticatedUser->user_type, $adminIds)) {
                return response($this->repository->getAll($request));
            }

            if($user_id = $request->get('user_id')) {
                return response($this->repository->getUsersJobs($user_id));
            }

            return response("No records found");

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
        
        /*
            - there was no default response
            - I have used early returns
            - changed admin condition || with in_array
        */
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try {
            $job = $this->repository->with('translatorJobRel.user')->find($id);

            return response($job ?? "Not found");
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        try {

            $data = $request->all();

            $response = $this->repository->store($request->__authenticatedUser, $data);

            return response($response);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
        
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        try {

            $data = $request->except(['_token', 'submit']);
            $cuser = $request->__authenticatedUser;
            $response = $this->repository->updateJob($id, $data, $cuser);

            return response($response);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
        

        /*
            - replaced array_except with laravel $request->except
        */
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        try {
            $data = $request->all();
            $response = $this->repository->storeJobEmail($data);

            return response($response); 

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
        
        /*
            - removed unused var $adminSenderEmail
        */
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        try {

            if($user_id = $request->get('user_id')) {
                $response = $this->repository->getUsersJobsHistory($user_id, $request);
                return response($response);
            }

            return response("No history found");

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
        

        /*
            - return default response instead of null
        */
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        try {
            $data = $request->all();
            $user = $request->__authenticatedUser;

            $response = $this->repository->acceptJob($data, $user);

            return response($response);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
        
    }

    public function acceptJobWithId(Request $request)
    {
        try {

            $data = $request->get('job_id');
            $user = $request->__authenticatedUser;

            $response = $this->repository->acceptJobWithId($data, $user);

            return response($response);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
        
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        try {

            $data = $request->all();
            $user = $request->__authenticatedUser;

            $response = $this->repository->cancelJobAjax($data, $user);

            return response($response); 
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
        
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        try {

            $data = $request->all();

            $response = $this->repository->endJob($data);

            return response($response);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
        

    }

    public function customerNotCall(Request $request)
    {
        try {

            $data = $request->all();

            $response = $this->repository->customerNotCall($data);

            return response($response);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
        
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        try {

            $data = $request->all();
            $user = $request->__authenticatedUser;

            $response = $this->repository->getPotentialJobs($user);

            return response($response);

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
        
    }

    public function distanceFeed(Request $request)
    {
        try {
           $data = $request->all();

            $distance = $data['distance'] ?? "";

            $time = $data['time'] ?? "";

            if(!empty($data['jobid'])) {
                $jobid = $data['jobid'];
            }

            $session = $data['session_time'] ?? "";

            $flagged = $data['flagged'] == 'true' ? 'yes' : 'no';
            $admincomment = $data['admincomment'] ?? "";

            if($flagged && !$admincomment) {
                return "Please, add comment";
            }
            
            $manually_handled = $data['manually_handled'] == 'true' ? 'yes' : 'no';

            $manually_handled = $data['by_admin'] == 'true' ? 'yes' : 'no';

            if ($time || $distance) {

                $affectedRows = Distance::where('job_id', '=', $jobid)->update(array('distance' => $distance, 'time' => $time));
            }

            if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {

                $affectedRows1 = Job::where('id', '=', $jobid)->update(array('admin_comments' => $admincomment, 'flagged' => $flagged, 'session_time' => $session, 'manually_handled' => $manually_handled, 'by_admin' => $by_admin));

            }

            return response('Record updated!'); 

        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
        

        /*
            - replaced if else conditions with null coalescing operator or ternary operator
        */
    }

    public function reopen(Request $request)
    {
        try {
            $data = $request->all();
            $response = $this->repository->reopen($data);

            return response($response);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
    }

    public function resendNotifications(Request $request)
    {
        try {
            $data = $request->all();
            $job = $this->repository->find($data['jobid']);
            $job_data = $this->repository->jobToData($job);
            $this->repository->sendNotificationTranslator($job, $job_data, '*');

            return response(['success' => 'Push sent']);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        try {
            $data = $request->all();
            $job = $this->repository->find($data['jobid']);
            $job_data = $this->repository->jobToData($job);

            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage()]);
        }
    }

}
