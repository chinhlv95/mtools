<?php

namespace App\Http\Controllers;

use App\Repositories\Import\ImportRepositoryInterface;
use Config;
use Illuminate\Http\Request;
use Input;
use Illuminate\Pagination\LengthAwarePaginator;

class FileManagementController extends Controller {

    public function __construct(ImportRepositoryInterface $file) {
        $this->file = $file;
    }

    public function index(Request $request) {

        $limit       = $request->get('limit', 10);
        $status_file = Config::get('constant.status_file');
        $paginate    = Config::get('constant.paginate_number');
        $stt         = ( $request->get('page', '1') - 1 ) * $limit;

        $array = $this->file->getFileManagement();
        $page = Input::get('page', 1); // Get the current page or default to 1, this is what you miss!
        $perPage = $limit;
        $offset = ($page * $perPage) - $perPage;
        
        $result = new LengthAwarePaginator(
                    array_slice($array, $offset, $perPage, true),
                    count($array),
                    $perPage,
                    $page,
                    [
                        'path' => $request->url(),
                        'query' => $request->query()
                    ]);

        return view('file_management.index', [
            'files' => $result,
            'paginate_number' => $paginate,
            'status_file' => $status_file,
            'stt' => $stt,
        ]);
    }

}
