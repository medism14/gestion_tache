<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $users = User::all();

        $users = User::paginate(5);

        return view('users.index', compact('users'));
    }

    public function getUserInfo(Request $request, $id)
    {
        // Récupérer les informations de l'utilisateur correspondant à $id depuis la base de données
        $user = User::find($id);

        // Renvoyer les informations sous forme de réponse JSON
        return response()->json($user);
    }

    public function PersonnalInformation(Request $request)
    {
        $id = auth()->user()->id;

        $user = User::find($id);

        return view('users.perso', compact('user'));
    }

    public function search(Request $request)
    {
        $page = $request->query('page');

        $page = intval($page);

        if ($page != null) {

             $role = session('role');
             $search = session('search');
             $filter = session('filter');

        }else {
            $role = $request->get('role');
            $search = $request->get('search');
            $filter = $request->get('filter');

            session(['role' => $role]);
            session(['search' => $search]);
            session(['filter' => $filter]);
        }

        $role = intval($role);

        function transform_string($string) {
          $result = [
            'lower' => strtolower($string),
            'upper' =>strtoupper($string),
            'first_word' =>ucfirst(strtolower($string))
          ];
          return $result;
        }

        if ($filter == 'first_name')
        {   

            $result = transform_string($search);

            $users = User::where('role', $role)
             ->where(function($query) use ($filter, $result) {
                 $query->where($filter, $result['upper'])
                       ->orWhere($filter, $result['lower'])
                       ->orWhere($filter, $result['first_word']);
             })
             ->paginate(5);

        }
        else if ($filter == 'email')
        {

            $users = User::where('role',$role)
            ->where('email',$search)
            ->paginate(5);

        }
        else if ($filter == 'phone')
        {

            $users = User::where('role',$role)
            ->where('phone',$search)
            ->paginate(5);

        }
        else if ($filter == 'role')
        {
            $users = User::where('role',$role)
                     ->paginate(5);
        }

            $search = 1;

        return view('users.index',compact('users', 'search'));
    }

    public function create(Request $request)
    {

        function tab($chaine)
        {
            $i = 0;
            $row = 1;
            $first_name = '';
            $last_name = '';
            $role = '';
            $email = '';
            $phone = '';
            for ($i = 0; $i < strlen($chaine); $i++)
            {
                if ($chaine[$i] == ';')
                {
                    $row++;
                    continue;
                }

                if ($row == 1)
                {
                    $first_name .= $chaine[$i]; // Note: $first_name correspondra à 'first_name' dans le validateur
                }
                else if ($row == 2)
                {
                    $last_name .= $chaine[$i]; // Note: $name correspondra à 'name' dans le validateur
                }
                else if ($row == 3)
                {
                    $email .= $chaine[$i]; // Note: $email correspondra à 'email' dans le validateur
                }
                else if ($row == 4)
                {
                    $phone .= $chaine[$i]; // Note: $phone correspondra à 'phone' dans le validateur
                }
                else if ($row == 5)
                {
                    $role .= $chaine[$i]; // Note: $role correspondra à 'role' dans le validateur
                }
            }

            $cols = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone,
                'role' => $role,
                );

            return $cols;
        }

            if ($request->has('csv_file')) {
                $file = $request->file('csv_file');
                $csvData = file_get_contents($file);
                $rows = array_map('str_getcsv', explode("\n", $csvData));
                $header = array_shift($rows);
                $i = 0;
                foreach ($rows as $row) 
                {
                    $cols = tab($row[0]);

                    if ($cols['last_name'] == "") {
                        break;
                    }

                    $validator = Validator::make($cols, [
                        'first_name' => ['required', 'string', 'max:255'],
                        'last_name' => ['required', 'string', 'max:255'],
                        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                        'phone' => ['required', 'integer', 'min:77000000', 'max:77999999', 'unique:users'],
                        'role' => ['required', 'integer', 'min:0', 'max:1'],
                    ], [
                        'first_name.required' => 'Le prénom est obligatoire.',
                        'first_name.string' => 'Le prénom doit être une chaîne de caractères.',
                        'first_name.max' => 'Le prénom ne doit pas dépasser :max caractères.',
                        'last_name.required' => 'Le nom est obligatoire.',
                        'last_name.string' => 'Le nom doit être une chaîne de caractères.',
                        'last_name.max' => 'Le nom ne doit pas dépasser :max caractères.',
                        'email.required' => "L'adresse e-mail est obligatoire.",
                        'email.string' => "L'adresse e-mail doit être une chaîne de caractères.",
                        'email.email' => "Veuillez saisir une adresse e-mail valide.",
                        'email.max' => "L'adresse e-mail ne doit pas dépasser :max caractères.",
                        'email.unique' => "Cette adresse e-mail est déjà utilisée.",
                        'phone.required' => 'Le numéro de téléphone est obligatoire.',
                        'phone.integer' => 'Le numéro de téléphone doit être un entier.',
                        'phone.min' => 'Le numéro de téléphone doit être minimum de 77000000.',
                        'phone.max' => 'Le numéro de téléphone doit être au maximum de 77999999',
                        'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
                        'role.required' => 'Le rôle est obligatoire.',
                        'role.integer' => 'Le rôle doit être un entier.',
                        'role.min' => 'Le rôle doit être au minimum de 0.',
                        'role.max' => 'Le rôle doit être au minimum de 1.',
                        ]);
                            

                        if ($validator->fails()) {
                            return redirect()->route('users')->withErrors($validator->errors());
                        }

                        $generated_password = 'password';

                        $user = User::create([
                            'first_name' => $cols['first_name'],
                            'last_name' => $cols['last_name'],
                            'email' => $cols['email'],
                            'phone' => $cols['phone'],
                            'role' => $cols['role'],
                            'first_connection' => 1,
                            'password' => Hash::make($generated_password)
                        ]);

                        $i++;

                    }

                    $result = ($i > 1) ? "Les utilisateurs ont bien étés créé(e)s" : "L'utilisateur a bien été créé(e)";

                return redirect(route('users'))->with('success', $result);
            }else {

            if (!auth()->check())
            {
                $validator = Validator::make($request->all(), [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone' => ['required', 'integer', 'min:77000000', 'max:77999999', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ], [
                'first_name.required' => 'Le prénom est obligatoire.',
                'first_name.string' => 'Le prénom doit être une chaîne de caractères.',
                'first_name.max' => 'Le prénom ne doit pas dépasser :max caractères.',
                'last_name.required' => 'Le nom est obligatoire.',
                'last_name.string' => 'Le nom doit être une chaîne de caractères.',
                'last_name.max' => 'Le nom ne doit pas dépasser :max caractères.',
                'email.required' => "L'adresse e-mail est obligatoire.",
                'email.string' => "L'adresse e-mail doit être une chaîne de caractères.",
                'email.email' => "Veuillez saisir une adresse e-mail valide.",
                'email.max' => "L'adresse e-mail ne doit pas dépasser :max caractères.",
                'email.unique' => "Cette adresse e-mail est déjà utilisée.",
                'phone.required' => 'Le numéro de téléphone est obligatoire.',
                'phone.integer' => 'Le numéro de téléphone doit être un entier.',
                'phone.min' => 'Le numéro de téléphone doit être minimum de 77000000.',
                'phone.max' => 'Le numéro de téléphone doit être au maximum de 77999999',
                'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
                'password.min' => 'Le mot de passe doit comporter au moins :min caractères.',
                'password.confirmed' => 'Le mot de passe ne correspond pas à la confirmation.',
                ]);
            }
            else {
                $validator = Validator::make($request->all(), [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone' => ['required', 'integer', 'min:77000000', 'max:77999999', 'unique:users'],
                'role' => ['required', 'integer', 'min:0', 'max:1'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ], [
                'first_name.required' => 'Le prénom est obligatoire.',
                'first_name.string' => 'Le prénom doit être une chaîne de caractères.',
                'first_name.max' => 'Le prénom ne doit pas dépasser :max caractères.',
                'last_name.required' => 'Le nom est obligatoire.',
                'last_name.string' => 'Le nom doit être une chaîne de caractères.',
                'last_name.max' => 'Le nom ne doit pas dépasser :max caractères.',
                'email.required' => "L'adresse e-mail est obligatoire.",
                'email.string' => "L'adresse e-mail doit être une chaîne de caractères.",
                'email.email' => "Veuillez saisir une adresse e-mail valide.",
                'email.max' => "L'adresse e-mail ne doit pas dépasser :max caractères.",
                'email.unique' => "Cette adresse e-mail est déjà utilisée.",
                'phone.required' => 'Le numéro de téléphone est obligatoire.',
                'phone.integer' => 'Le numéro de téléphone doit être un entier.',
                'phone.min' => 'Le numéro de téléphone doit être minimum de 77000000.',
                'phone.max' => 'Le numéro de téléphone doit être au maximum de 77999999',
                'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
                'role.required' => 'Le rôle est obligatoire.',
                'role.integer' => 'Le rôle doit être un entier.',
                'role.min' => 'Le rôle doit être au minimum de 0.',
                'role.max' => 'Le rôle doit être au minimum de 1.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
                'password.min' => 'Le mot de passe doit comporter au moins :min caractères.',
                'password.confirmed' => 'Le mot de passe ne correspond pas à la confirmation.',
                ]);

            }
            
            if(auth()->check() && $validator->fails())
            {
                return redirect()->route('users')->withErrors($validator->errors());
            }
            else if (!auth()->check() && $validator->fails())
            {
                return redirect()->route('register')->withErrors($validator->errors());
            }
            

            $new_user = new User;

            if (auth()->check())
            {
                $role = $request->input('role');
            }
            else
            {
                $role = 1;
            }

            $generated_password = $request->input('password');

            $user = User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'role' => $role,
                'first_connection' => 1,
                'password' => Hash::make($generated_password)
            ]);
            
            if (Auth::check()) {
                // Vous pouvez renvoyer une réponse JSON dans les deux cas
                return redirect()->route('users')->with('success', "L'utilisateur a bien été créé.");
            } else {
                return redirect()->route('login')->with('success', "L'utilisateur a bien été créé.");
            }

            }   
    }

    public function edit(Request $request)
    {

        $id = $request->input('id');

        if ($request->input('perso'))
        {
            $perso = 1;
        }

        $user = User::findorFail($id);

        $requestData = $request->all();

        if ($user->email == $request->input('email')){
            if ($user->phone == $request->input('phone')){
                $phone = $requestData['phone'];
                $requestData['phone'] = '77667667';
                $request->replace($requestData);
            }

            $email = $requestData['email'];
            $requestData['email'] = 'passage@gmail.com';
            $request->replace($requestData);

                $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'integer', 'min:77000000', 'max:77999999', 'unique:users'],
            'role' => ['required', 'integer', 'min:0', 'max:1'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'first_name.required' => 'Le prénom est obligatoire.',
            'first_name.string' => 'Le prénom doit être une chaîne de caractères.',
            'first_name.max' => 'Le prénom ne doit pas dépasser :max caractères.',
            'last_name.required' => 'Le nom est obligatoire.',
            'last_name.string' => 'Le nom doit être une chaîne de caractères.',
            'last_name.max' => 'Le nom ne doit pas dépasser :max caractères.',
            'email.required' => "L'adresse e-mail est obligatoire.",
            'email.string' => "L'adresse e-mail doit être une chaîne de caractères.",
            'email.email' => "Veuillez saisir une adresse e-mail valide.",
            'email.max' => "L'adresse e-mail ne doit pas dépasser :max caractères.",
            'email.unique' => "Cette adresse e-mail est déjà utilisée.",
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.integer' => 'Le numéro de téléphone doit être un entier.',
            'phone.min' => 'Le numéro de téléphone doit être minimum de 77000000.',
            'phone.max' => 'Le numéro de téléphone doit être au maximum de 77999999',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.integer' => 'Le rôle doit être un entier.',
            'role.min' => 'Le rôle doit être au minimum de 0.',
            'role.max' => 'Le rôle doit être au minimum de 1.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit comporter au moins :min caractères.',
            'password.confirmed' => 'Le mot de passe ne correspond pas à la confirmation.',
            ]);
            

            if ($validator->fails()) {
                if (isset($perso))
                {
                    return redirect()->route('personnal.informations')->withErrors($validator->errors());
                }
                else
                {
                    return redirect()->route('users')->withErrors($validator->errors());
                }
            }

        }else{
            if ($user->phone == $request->input('phone')){
                $phone = $requestData['phone'];
                $requestData['phone'] = '77667667';
                $request->replace($requestData);
            }
            $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'integer', 'min:77000000', 'max:77999999', 'unique:users'],
            'role' => ['required', 'integer', 'min:0', 'max:1'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'first_name.required' => 'Le prénom est obligatoire.',
            'first_name.string' => 'Le prénom doit être une chaîne de caractères.',
            'first_name.max' => 'Le prénom ne doit pas dépasser :max caractères.',
            'last_name.required' => 'Le nom est obligatoire.',
            'last_name.string' => 'Le nom doit être une chaîne de caractères.',
            'last_name.max' => 'Le nom ne doit pas dépasser :max caractères.',
            'email.required' => "L'adresse e-mail est obligatoire.",
            'email.string' => "L'adresse e-mail doit être une chaîne de caractères.",
            'email.email' => "Veuillez saisir une adresse e-mail valide.",
            'email.max' => "L'adresse e-mail ne doit pas dépasser :max caractères.",
            'email.unique' => "Cette adresse e-mail est déjà utilisée.",
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.integer' => 'Le numéro de téléphone doit être un entier.',
            'phone.min' => 'Le numéro de téléphone doit être minimum de 77000000.',
            'phone.max' => 'Le numéro de téléphone doit être au maximum de 77999999',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.integer' => 'Le rôle doit être un entier.',
            'role.min' => 'Le rôle doit être au minimum de 0.',
            'role.max' => 'Le rôle doit être au minimum de 1.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.string' => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min' => 'Le mot de passe doit comporter au moins :min caractères.',
            'password.confirmed' => 'Le mot de passe ne correspond pas à la confirmation.',
            ]);
            

            if ($validator->fails()) {
                if (isset($perso))
                {
                    return redirect()->route('personnal.informations')->withErrors($validator->errors());
                }
                else
                {
                    return redirect()->route('users')->withErrors($validator->errors());
                }
            }
        }

        if (isset($email)){
            $email = $email;
        }
        else{
            $email = $request->input('email');   
        }

        if(isset($phone)){
            $phone = $phone;
        }else{
            $phone = $request->input('phone');
        }

        if ($request->password == null){
            $user->update([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $email,
                'phone' => $phone,
                'role' => $request->input('role'),
            ]);
        }else{
            $generated_password = $request->input('password'); 

            $user->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $email,
            'phone' => $phone,
            'role' => $request->input('role'),
        ]);
        }
            if (isset($perso))
            {
                return redirect()->route('personnal.informations')->with('success', "L'utilisateur a bien été modifié.");
            }
            else
            {
                return redirect()->route('users')->with('success', "L'utilisateur a bien été modifié.");
            }
    }

    public function delete(string $id)
    {
        $user = User::find($id);

        if ($user) {
            // Suppression de l'utilisateur
            $user->delete();

            // Redirection vers la liste des utilisateurs avec un message de succès
            return redirect()->route('users')->with('delete', 'L\'utilisateur a été supprimé avec succès.');
        } else {
            // Redirection vers la liste des utilisateurs avec un message d'erreur
            return redirect()->route('users')->with('delete', 'L\'utilisateur n\'existe pas ou a déjà été supprimé.');
        }
    }


}
