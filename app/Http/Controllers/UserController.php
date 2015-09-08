<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator;
use Input;
use Session;
use Redirect;
use Hash;
use Auth;

use App\User;

class UserController extends Controller {

	public function change_password()
	{
		return view('auth.change_password');
	}
	
	public function change_password_save()
	{
		$rules = array(
            'password_old'       => 'required',
            'password_new' => 'required',
            'password_repeat' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
		if(Input::get('password_new') != Input::get('password_repeat')){
			$validator->after(function($validator){
				$validator->errors()->add('field', 'Новый пароль не правильно повторен');
			});
		}
		
		$hashed = Hash::make(Input::get('password_old'));
		
		if (!Hash::check(Input::get('password_old'), Auth::user()->password)) {
			$validator->after(function($validator){
				$validator->errors()->add('field', 'Неправильно введен старый пароль');
			});
		}
		
		// process the login
        if ($validator->fails()) {
            return redirect()->route('change_password')
                ->withErrors($validator);
        } else {
            $user = Auth::user();
			$user->password = Hash::make(Input::get('password_new'));
			$user->save();
			Auth::loginUsingId($user->id);
			
			// redirect
            Session::flash('message', 'Пароль успешно изменен');
            return redirect()->route('admin');
        }
		

		
	}
	


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$users = User::latest('level')->get();
		return view('admin.users.index')->with('users', $users);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.users.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// validate

        $rules = array(
            'name'       => 'required',
            'email'      => 'required|email',
            'level' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
		if(Input::get('password') != Input::get('password_confirmation')){
			$validator->after(function($validator){
				$validator->errors()->add('field', 'Пароли не совпадают');
			});
		}
		
	
        // process the login
        if ($validator->fails()) {
            return redirect()->route('admin.users.create')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            $user = new User;
            $user->name       = Input::get('name');
            $user->email      = Input::get('email');
            $user->level = Input::get('level');
            $user->password = Hash::make(Input::get('password'));
            $user->save();

            // redirect
            Session::flash('message', 'Пользователь успешно добавлен');
            return redirect()->route('admin.users.index');
        }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		// get the nerd
        $user = User::find($id);

		return view('admin.users.edit')->with('user', $user);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// validate
        // read more on validation at http://laravel.com/docs/validation
        $rules = array(
            'name'       => 'required',
            'email'      => 'required|email',
            'level' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);

        // process the login
        if ($validator->fails()) {
            return redirect()->route('admin.users.edit', $id)
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store
            $user = User::find($id);
            $user->name = Input::get('name');
            $user->email = Input::get('email');
            $user->level = Input::get('level');
            $user->save();

            // redirect
            Session::flash('message', 'Пользователь успешно отредактирован!');
            return redirect()->route('admin.users.index');
        }
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		
		$rules = array();
        $validator = Validator::make(Input::all(), $rules);
		if (Auth::check() and Auth::user()->id == $id){
			$validator->after(function($validator){
				$validator->errors()->add('field', 'Нельзя удалить себя');
			});
		}
		

        if ($validator->fails()) {
            return redirect()->route('admin.users.index')->withErrors($validator);
        } else {

			// delete
			$user = User::find($id);
			$user->delete();

			// redirect
			Session::flash('message', 'Пользователь успешно удален!');
			return redirect()->route('admin.users.index');
		}
	}

}
