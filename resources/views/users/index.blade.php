@extends('layouts.app')

@section('content')
<style>
.custom-file-upload {
  border: 1px solid #ccc;
  display: inline-block;
  padding: 6px 12px;
  cursor: pointer;
  border-radius: 4px;
  background-color: #f1f1f1;
}

.custom-file-upload:hover {
  background-color: #e6e6e6;
}

/* Facultatif : ajustez la couleur et la taille de l'icône */
.custom-file-upload i {
  color: #007bff;
}

</style>

  <div class="card">
    <div class="card-header">
      <div class="row d-flex align-items-center">
        <div class="col-md-3">
          <h3 class="card-title">Gestion d'utilisateurs</h3>
        </div>  
        <div class="col-md-9 d-flex justify-content-md-end justify-content-sm-start">
          <button class="btn btn-primary mt-2" href="#" data-bs-placement="left" title="Créer un nouveau utilisateur" id="addUserBtn">
            <i class="fas fa-plus"></i>
          </button>
        </div>
      </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <div class="row">
        <div class="col-md-1"></div>
          <div class="col-md-10">
            <form action="{{ route('user_search') }}" method="POST">
              @csrf
              <div class="d-md-flex mb-3">
                <select class="form-select text-dark" id="roleSelect" name="role">
                  <option value="0">Administrateur</option>
                  <option value="1">Utilisateur</option>
                </select>

                <input type="text" class="form-control mt-1" placeholder="Recherche..." name="search" id="searchInput" required>

                <select class="form-select text-dark mt-1" id="filterSelect" name="filter">
                  <option value="first_name" selected>Prenom</option>
                  <option value="email">Email</option>
                  <option value="phone">Téléphone</option>
                  <option value="role">Role</option>
                </select>

                <button type="submit" class="btn btn-primary mt-1">Rechercher</button>
              </div>
            </form>
          </div>
        <div class="col-md-1"></div>
      </div>
      @if(session('success'))
        <div class="alert alert-success" id="successMessage">
          {{ session('success') }}
        </div>
      @endif

      @if(session('delete'))
        <div class="alert alert-danger" id="successMessage">
          {{ session('delete') }}
        </div>
      @endif

      <!-- Table d'informations -->
      <table id="example1" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th></th>
            <th>Prenom</th>
            <th>Nom</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @php
            $i = ($users->currentPage() - 1) * $users->perPage() + 1;
          @endphp
          @forelse($users as $item)
            <tr>
              <td class="id">{{ $i++ }}</td>
              <td class="id d-none">{{ $item->id }}</td>
              <td>{{ $item->first_name }}</td>
              <td>{{ $item->last_name }}</td>
              <td>
                {{ $item->role == '1' ? 'Utilisateur' : ''}}
                {{ $item->role == '0' ? 'Administrateur' : ''}}
              </td>
              <td>
                <div class="d-flex justify-content-between align-items-center">
                  <button href="#" class="text-primary viewUserBtn mr-2 btn" onclick="return false;" data-bs-placement="top" title="Voir l'utilisateur">
                  <i class="fas fa-eye"></i>
                  </button>
                  <button href="#" class="text-warning editUserBtn mr-2 btn" onclick="return false;" data-bs-placement="top" title="Modifier l'utilisateur">
                    <i class="fas fa-edit"></i>
                  </button>
                  <a href="{{ route('user_delete',['id' => $item->id]) }}" class="text-danger deleteUserBtn btn" 
                  onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.');" data-bs-placement="left" title="Supprimer l'utilisateur">
                  <i class="fas fa-trash-alt"></i>
                  </a>
                </div>
              </td>
            </tr>
          @empty
          <!-- Affichage du texte d'absence de données -->
            <tr>
              <td colspan="6" class="no-data-text text-center">Aucune donnée disponible dans la table</td>
            </tr>
          @endforelse
        </tbody>

        <tfoot>
          <tr>
            <th></th>
            <th>Prenom</th>
            <th>Nom</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  @if (isset($search))
    <div class="container d-flex justify-content-center align-items-center mt-2">
      <a href="/gestion_utilisateur" class="btn btn-primary">Revenir à la page d'index</a>
    </div>
  @endif

  <!-- Pagination -->
  <div class="pagination d-flex justify-content-center mt-4">
    <ul class="pagination">
      <!-- Lien pour revenir à la première page -->
      @if ($users->onFirstPage())
        <li class="page-item disabled">
          <span class="page-link">&laquo;</span>
        </li>
      @else
        <li class="page-item">
          <a class="page-link" href="{{ $users->url(1) }}" aria-label="Première page" data-bs-placement="top" title="Première page" id="first_page">&lt;&lt;</a>
        </li>
        <li class="page-item">
          <a class="page-link" href="{{ $users->previousPageUrl() }}" rel="prev" data-bs-placement="top" title="Page précedente" id="previous_page">&laquo;</a>
        </li>
      @endif

      @for ($i = 1; $i <= $users->lastPage(); $i++)
        @if ($i == $users->currentPage())
          <li class="page-item active">
            <span class="page-link">{{ $i }}</span>
          </li>
        @else
          <li class="page-item">
            <a class="page-link" href="{{ $users->url($i) }}">{{ $i }}</a>
          </li>
        @endif
      @endfor

      @if ($users->hasMorePages())
        <li class="page-item">
          <a class="page-link" href="{{ $users->nextPageUrl() }}" rel="next" data-bs-placement="top" title="Page suivante" id="next_page">&raquo;</a>
        </li>
        <!-- Lien pour revenir à la dernière page -->
      <li class="page-item">
        <a class="page-link" href="{{ $users->url($users->lastPage()) }}" aria-label="Dernière page" data-bs-placement="top" title="Dernière page" id="last_page">&gt;&gt;</a>
      </li>
      @else
        <li class="page-item disabled">
          <span class="page-link">&raquo;</span>
        </li>
      @endif
    </ul>
  </div>

                <!---------------------------- Modal ajout utilisateur ---------------------------->
  <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Créer un nouvel utilisateur</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('user_create') }}" id="addUserForm" enctype="multipart/form-data">
            @csrf
            <div class="row d-flex justify-content-center align-items-center">
              <label for="file" class="custom-file-upload" style="max-width: 200px; height: 40px;">
                <i class="fas fa-file-excel"></i> Choisir un fichier CSV
              </label>
              <input type="file" id="file" name="csv_file" accept=".csv" style="display:none;">
              <div id="file-name-display" class="d-block text-center text-muted fs-6 text-decoration-underline mb-3"></div>
            </div>
            
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">  
                  <label for="first_name">Prénom</label>
                  <input type="text" class="form-control" id="first_name" name="first_name" required>
                </div>

                <div class="form-group">
                  <label for="last_name">Nom</label>
                  <input type="text" class="form-control" id="last_name" name="last_name" required>
                </div>

                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" required>
                </div>
              </div>
                          
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="phone">Phone</label>
                  <input type="number" class="form-control" id="phone" name="phone" required>
                </div>

                <div class="form-group">
                  <label for="role">Role</label>
                  <select class="form-select" id="role" name="role" required>
                    @foreach (\App\Enums\UserRoleEnum::cases() as $role)
                      <option value="{{ $role->value }}">{{ $role->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group">
                  <label for="password">Mot de passe</label>
                  <input type="password" class="form-control" id="password" name="password" required>
                </div>
              </div>

                <div class="form-group">
                  <label for="password_confirmation">Confirmer le mot de passe</label>
                  <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
        
                <div class="d-flex justify-content-center">
                  <button type="submit" class="btn btn-primary me-2" id="createUserBtn">Ajouter</button>
                  <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">Annuler</button>
                </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div> 

          <!---------------------------------- Modal vue utilisateur -------------------------------->
  <div class="modal" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hadden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewUserModalLabel"></h5>
          <button class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">  
                <label for="first_name">Prénom</label>
                <input readonly type="text" class="form-control" id="view_first_name" name="vue_first_name" required>
              </div>

              <div class="form-group">
                <label for="name">Nom</label>
                <input readonly type="text" class="form-control" id="view_last_name" name="last_name" required>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group">
                <label for="phone">Phone</label>
                <input readonly type="tel" class="form-control" id="view_phone" name="phone" required>
              </div>

              <div class="form-group">
                <label for="role">Role</label>
                <input readonly class="form-control" id="view_role" name="role" required>
              </div>
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input readonly type="email" class="form-control" id="view_email" name="email" required>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
          <!---------------------------------- Modal edit utilisateur -------------------------------->
  <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Modifier l'utilisateur </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('user_update') }}" id="editUserForm" onsubmit="return confirm('Êtes-vous sûr de vouloir mettre à jour cet utilisateur ?');">
            @csrf
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group d-none">  
                  <label for="first_name">ID</label>
                  <input type="text" class="form-control" id="edit_id" name="id" required>
                </div>

                <div class="form-group">  
                  <label for="first_name">Prénom</label>
                  <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                </div>

                <div class="form-group">
                  <label for="name">Nom</label>
                  <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                </div>

                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="edit_email" name="email" required>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="form-group">
                  <label for="phone">Phone</label>
                  <input type="tel" class="form-control" id="edit_phone" name="phone" required>
                </div>

                <div class="form-group">
                  <label for="role">Role</label>
                  <select class="form-select" id="edit_role" name="role" required>
                  </select>
                </div>
  
                <div class="form-group">
                  <label for="password">Mot de passe</label>
                  <input type="password" class="form-control" id="edit_password" name="password">
                </div>
              </div>

                <div class="form-group">
                  <label for="password_confirmation">Confirmer le mot de passe</label>
                  <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation">
                </div>

                <div class="d-flex justify-content-center">
                  <button type="submit" class="btn btn-primary me-2" id="editUserBtn">Modifier</button>
                  <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">Annuler</button>
                </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div> 

@endsection

@section('scripts')
    <script>

document.addEventListener('DOMContentLoaded', function() {
        // Activer le tooltip

        var tooltip_adduserBtn = new bootstrap.Tooltip(document.getElementById('addUserBtn'));

        var viewUserBtns = document.querySelectorAll('.viewUserBtn');
        viewUserBtns.forEach(function(btn) {
            new bootstrap.Tooltip(btn);
        });

        var editUserBtns = document.querySelectorAll('.editUserBtn');
        editUserBtns.forEach(function(btn) {
            new bootstrap.Tooltip(btn);
        });

        var deleteUserBtns = document.querySelectorAll('.deleteUserBtn');
        deleteUserBtns.forEach(function(btn) {
            new bootstrap.Tooltip(btn);
        });

        // Vérifie si l'élément avec l'ID 'first_page' existe
        var firstPageElement = document.getElementById('first_page');
        if (firstPageElement) {
          var tooltip_first_page = new bootstrap.Tooltip(firstPageElement);
        }

        // Vérifie si l'élément avec l'ID 'previous_page' existe
        var previousPageElement = document.getElementById('previous_page');
        if (previousPageElement) {
          var tooltip_previous_page = new bootstrap.Tooltip(previousPageElement);
        }

        // Vérifie si l'élément avec l'ID 'next_page' existe
        var nextPageElement = document.getElementById('next_page');
        if (nextPageElement) {
          var tooltip_next_page = new bootstrap.Tooltip(nextPageElement);
        }

        // Vérifie si l'élément avec l'ID 'last_page' existe
        var lastPageElement = document.getElementById('last_page');
        if (lastPageElement) {
          var tooltip_last_page = new bootstrap.Tooltip(lastPageElement);
        }

        });
        


  /////////////////////////////////
  //EDIT USER//////////////////////
  /////////////////////////////////

// Récupérer tous les boutons avec la classe 'editUserBtn'
const editButtons = document.getElementsByClassName('editUserBtn');

// Ajouter un écouteur d'événement à chaque bouton
for (const button of editButtons) {
  button.addEventListener('click', function() {
    button.disabled = true;

    setTimeout(function () {
        button.disabled = false;
    }, 1000);

    var tr = event.target.parentNode.parentNode.parentNode.parentNode;
    var id = tr.children[1].textContent;

    fetch('/get_user_info/' + id)
      .then(response => response.json())
      .then(data => {
        // Récupération des éléments DOM par leur ID

        let header_name = document.getElementById('editUserModalLabel');
        let edit_id = document.getElementById('edit_id');
        let edit_first_name = document.getElementById('edit_first_name');
        let edit_last_name = document.getElementById('edit_last_name');
        let edit_email = document.getElementById('edit_email');
        let edit_phone = document.getElementById('edit_phone');
        let edit_role = document.getElementById('edit_role');
        let edit_password = document.getElementById('edit_password');

        // Mise à jour du contenu du libellé "viewUserModalLabel" avec le prénom et le nom de l'utilisateur
        header_name.innerText = data.first_name + ' ' + data.last_name;

        // Mise à jour des valeurs des champs de saisie avec les données de l'utilisateur
        edit_first_name.value = data.first_name;
        edit_id.value = data.id;
        edit_last_name.value = data.last_name;
        edit_email.value = data.email;
        edit_phone.value = data.phone;

        // Vérification du rôle de l'utilisateur et mise à jour du champ de sélection "edit_role"

        const newOption_admin = document.createElement('option');
          const newOption_user = document.createElement('option');

          newOption_admin.textContent = 'Administrateur';
          newOption_admin.value = '0';
          newOption_user.textContent = 'Utilisateur';
          newOption_user.value = '1';

          edit_role.appendChild(newOption_admin);
          edit_role.appendChild(newOption_user);

        if (data.role == 0) {
          newOption_admin.selected = true;
          newOption_user.selected = false;
        } else {
          newOption_admin.selected = false;
          newOption_user.selected = true;
        }

        // Ouvrir le modal une fois les données mises à jour
        var myModal_edit = new bootstrap.Modal(document.getElementById('editUserModal'));
        myModal_edit.show();
      })
      .catch(error => {
        console.error(error);
      });

});

}

  /////////////////////////////////
  //VIEW USER//////////////////////
  /////////////////////////////////

// Récupérer tous les éléments ayant la classe 'fas fa-eye'
const viewButtons = document.getElementsByClassName('viewUserBtn');

// Ajouter un écouteur d'événement à chaque bouton d'affichage d'utilisateur
for (const button of viewButtons) {
  button.addEventListener('click', function(event) {

    button.disabled = true;

    setTimeout(function () {
        button.disabled = false;
    }, 1000);

    var tr = event.target.parentNode.parentNode.parentNode.parentNode;
    var id = tr.children[1].textContent;

    // Envoi de la requête AJAX
    fetch('/get_user_info/' + id)
      .then(response => response.json())
      .then(data => {
        // Récupération des éléments DOM par leur ID
        let header_name = document.getElementById('viewUserModalLabel');
        let view_first_name = document.getElementById('view_first_name');
        let view_last_name = document.getElementById('view_last_name');
        let view_email = document.getElementById('view_email');
        let view_phone = document.getElementById('view_phone');
        let view_role = document.getElementById('view_role');
        let view_password = document.getElementById('view_password');

        // Mise à jour du contenu du libellé "viewUserModalLabel" avec le prénom et le nom de l'utilisateur
        header_name.innerText = data.first_name + ' ' + data.last_name;

        // Mise à jour des valeurs des champs de saisie avec les données de l'utilisateur
        view_first_name.value = data.first_name;
        view_last_name.value = data.last_name;
        view_email.value = data.email;
        view_phone.value = data.phone;

        // Vérification du rôle de l'utilisateur et mise à jour du champ de sélection "view_role"
        if (data.role == 0) {
          view_role.value = 'Administrateur';
        } else {
          view_role.value = 'Utilisateur';
        }

        // Ouvrir le modal une fois les données mises à jour
        var myModal_view = new bootstrap.Modal(document.getElementById('viewUserModal'));
        myModal_view.show();
      })
      .catch(error => {
        console.error(error);
      });
  });
}


  /////////////////////////////////
  //ADD USER///////////////////////
  /////////////////////////////////

// Fonction pour ouvrir le modal lorsqu'on clique sur le bouton "Ajouter utilisateur"
        let addUserBtn =  document.getElementById('addUserBtn');
        addUserBtn.addEventListener('click', function() {
            addUserBtn.disabled = true;

            setTimeout(function () {
                addUserBtn.disabled = false;
            }, 1000);
          var myModal_add = new bootstrap.Modal(document.getElementById('addUserModal'));
          myModal_add.show();
        });

      



//Verification de mot de passe et affichage des erreurs
  
    @if ($errors->any())
    // Récupérer les messages d'erreur du tableau d'erreurs PHP et les afficher dans une alerte JavaScript
    var errorMessage = '';
    @foreach ($errors->all() as $error)
        errorMessage += "{{ $error }}" + "\n";
    @endforeach
    alert(errorMessage);
    @endif

    /////////////////////////////////////////////////////////
    //Pour faire disparaitre message succès//////////////////
    /////////////////////////////////////////////////////////

      $(document).ready(function() {
    // Cacher le message de succès après 3 secondes
    setTimeout(function() {
        $('#successMessage').fadeOut('slow');
      }, 3000);
    });

      $(document).ready(function() {
    // Cacher le message de delete après 3 secondes
    setTimeout(function() {
        $('#deleteMessage').fadeOut('slow');
      }, 3000);
    });

    /////////////////////////////////////////////////////////
    //Pour le fichier csv////////////////////////////////////
    /////////////////////////////////////////////////////////

    const input_file = document.getElementById('file');
const file_name_display = document.getElementById('file-name-display');

input_file.addEventListener('change', (event) => {
  const file = event.target.files[0]; // Récupère le fichier sélectionné (s'il y en a un)
  
  if (file) {
    const first_name = document.getElementById('first_name');
    const last_name = document.getElementById('last_name');
    const email = document.getElementById('email');
    const phone = document.getElementById('phone');
    const role = document.getElementById('role');
    const password = document.getElementById('password');
    const password_confirmation = document.getElementById('password_confirmation');

    first_name.disabled = true;
    first_name.value = '';
    last_name.disabled = true;
    last_name.value = '';
    email.disabled = true;
    email.value = '';
    phone.disabled = true;
    phone.value = '';
    role.disabled = true;
    password.disabled = true;
    password.value = '';
    password_confirmation.disabled = true;
    password_confirmation.value = '';

    file_name_display.textContent = file.name; // Affiche le nom du fichier
  } else {
    file_name_display.textContent = 'Aucun fichier sélectionné'; // Affiche un message si aucun fichier n'est sélectionné
  }
});

  //MODIFICATION BARRE DE RECHERCHE
  const filterSelect = document.getElementById('filterSelect');
  const searchInput = document.getElementById("searchInput");

  filterSelect.addEventListener('change', function (event) {
    let item = event.target;

    switch(filterSelect.value) {
    case "first_name":
      searchInput.type = "text";
      searchInput.classList.remove('d-none');
      searchInput.value = "";
      break;
    case "email":
      searchInput.type = "email";
      searchInput.classList.remove('d-none');
      searchInput.value = "";
      break;
    case "phone":
      searchInput.type = "number";
      searchInput.classList.remove('d-none');
      searchInput.value = "";
      break;
    case "role":
      const roleSelect = document.getElementById('roleSelect');
      searchInput.required = false;
      searchInput.classList.add('d-none');
  }


  });


  ////////////////////////////////////////////////////////////////////////////////////////////
  //Pour annuler l'envoie plusieurs fois//////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////

  let avoidForm = document.getElementById('addUserForm'); // Nom du formulaire d'ajout
  

    avoidForm.addEventListener('submit', function () {

    let avoidBtn = document.getElementById('createUserBtn'); // Nom du bouton d'ajout

    avoidBtn.disabled = true;

    setTimeout(function () {
        avoidBtn.disabled = false;
    }, 3000);
  });

 </script>
@endsection