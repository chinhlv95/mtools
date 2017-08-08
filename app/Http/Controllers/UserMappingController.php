<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Repositories\User\UserRepositoryInterface;
use function GuzzleHttp\json_encode;
use Illuminate\Support\Facades\Session;

/**
 * Mapping user by email
 * Feb 3, 2017 10:45:44 AM
 * @author tampt6722
 *
 */
class UserMappingController extends Controller
{

    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }

    public function index(Request $request)
    {
        $sourceName = '';
        $search = Session::get('search');
        $sourceId = Session::get('source');
        $paginate         = Config::get('constant.paginate_number');
        $limit            = $request->get('limit', Config::get('constant.RECORD_PER_PAGE'));
        $number           = ($request->get('page','1') - 1)* $limit;
        $sources          = Config::get('constant.stream_types');
        foreach ($sources as $key => $value) {
            if ($key == $sourceId) {
                $sourceName = $value;
                break;
            }
        }
        $users            = $this->user->getUserMapping($sourceId, $search)->paginate($limit);
        $emails = $this->user->getAllEmails()->toArray();
        $autoData = json_encode($emails);
        return view('user_mapping.index',[
                        'sources'  => $sources,
                        'sourceName' => $sourceName,
                        'limit' => $limit,
                        'users'    => $users,
                        'number'   => $number,
                        'paginate' => $paginate,
                        'autoData'  => $autoData,
        ]);
    }

    public function show(Request $request)
    {
        $sourceName = '';
        $search = $request->get('search', '');
        $sourceId = $request->get('source', '');
        $paginate         = Config::get('constant.paginate_number');
        $limit            = $request->get('limit', Config::get('constant.RECORD_PER_PAGE'));
        $number           = ($request->get('page','1') - 1)* $limit;
        $sources          = Config::get('constant.stream_types');
        $users            = $this->user->getUserMapping($sourceId, $search)->paginate($limit);
        foreach ($sources as $key => $value) {
            if ($key == $sourceId) {
                $sourceName = $value;
                break;
            }
        }
        $emails = $this->user->getAllEmails()->toArray();
        $autoData = json_encode($emails);
        return view('user_mapping.index',[
                        'sources'  => $sources,
                        'limit' => $limit,
                        'sourceName' => $sourceName,
                        'users'    => $users,
                        'number'   => $number,
                        'paginate' => $paginate,
                        'autoData'  => $autoData,
        ]);

    }

    public function update(Request $request)
    {
        $data = $request->all();
        $relatedData = [];
        $user = $this->user->findByAttribute('email', $data['main_email']);
        if ($data['main_email'] == '') {
            $parameters = [
                            'search' => $data['search'] ,
                            'source' => $data['source'],
                            'errorsMessage' => 'Please enter main email!'
            ];
            return redirect()->back()->with($parameters);
        } elseif (count($user) == 0) {
            $parameters = [
                            'search' => $data['search'] ,
                            'source' => $data['source'],
                            'errorsMessage' => 'Cannot find email '.$data['main_email']. ' !'
            ];
            return redirect()->back()->with($parameters);
        } elseif ((count($user) > 0) && ($user->id != $user->related_id)) {
            $parameters = [
                            'search' => $data['search'] ,
                            'source' => $data['source'],
                            'errorsMessage' => 'Email '.$data['main_email']. ' is a child of another email !'
            ];
            return redirect()->back()->with($parameters);
        } else {
            $relatedData['related_id'] = $user->id;
            $this->user->update($relatedData, $data['user_id']);
            $parameters = [
                            'search' => $data['search'] ,
                            'source' => $data['source'],
                            'success' => 'Update email '.$data['related_email']. ' success!',
            ];
            return redirect()->back()->with($parameters);
        }
    }
}