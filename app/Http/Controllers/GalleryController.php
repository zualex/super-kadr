<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Response;
use Session;
use Input;

class GalleryController extends Controller {


	public function index()
	{
		return view('pages.gallery.index');
	}

		
	public function create()
	{
		//session нужен для того чтобы определить картинку
		//check if its our form
        if ( Session::token() !== Input::get( '_token' ) ) {
            return Response::json( array(
				"status" => 'error',
				"message" => 'Проверка 1'
            ));
        }

		return Response::json( array(
			"status" => 'error',
			"message" => 'Проверка 2'
		));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		return view('pages.gallery.show');
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
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	
}