<?php

namespace App\Http\Controllers;

use Importer;
use App\ImportFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ImportExcelDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fileModel = ImportFile::all();
        return view('welcome', compact('fileModel'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx'
        ]);

        // $path = storage_path('app/public/uploads/1622571889_Book1.xlsx');


        $fileModel = new ImportFile();
        if ($request->file()) {
            $file = $request->file->getClientOriginalName();
            $ext = $request->file->extension();
            $fileName = time() . '_' . pathinfo($file, PATHINFO_FILENAME);
            $filePath = $request->file('file')->storeAs('uploads', $fileName . '.' . $ext, 'public');
            $url = '/storage/' . $filePath;
            // dd($fileName, $filePath);
            $fileModel->name = $fileName;
            $fileModel->url = $url;
            $fileModel->ext = $ext;
            $fileModel->save();
            if ($fileModel) {
                $this->makeTable($fileModel, $request);
            }

            return back()
                ->with('success', 'File has been uploaded.')
                ->with('file', $fileName);
        }
    }

    protected function makeTable($fileModel, $request)
    {
        // $path = $request->file('file')->getRealPath();
        $path = storage_path('app/public/uploads/') . $fileModel->name . '.' . $fileModel->ext;
        // dd($path);
        $excel = Importer::make('Excel');
        $excel->load($path);
        // dd($path);
        $collection = $excel->getCollection();
        $table_name = $fileModel->name;
        $arr = [];
        $count = 0;
        foreach ($collection as $columns) {
            if ($count == 0) {
                $count++;
                // check if table is not already exists
                if (!Schema::hasTable($table_name)) {
                    Schema::create($table_name, function (Blueprint $table)  use ($columns, $collection) {
                        $table->increments('id');
                        if (sizeof($collection[1]) > 0) {
                            for ($i = 0; $i < sizeof($collection[1]); $i++) {
                                $table->string($columns[$i], 255);
                            }
                        }
                        // $table->timestamps();
                    });
                }
                continue;
            }
            $attributes = Schema::getColumnListing($table_name);
            array_shift($attributes);

            // $attributes =  implode(",", $attributes);
            $attributes = "`" . implode("`, `", $attributes) . "`";
            $values = "'" . implode("', '", $columns) . "'";
            // echo $values;
            // exit;
            DB::insert("
                                    insert into $table_name ($attributes) values ($values);
                                ");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = ImportFile::find($id);
        $table_name = $model->name;
        $attributes = Schema::getColumnListing($table_name);
        $data = DB::table($table_name)->get();
        return view('filedata', compact('attributes', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    protected function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
