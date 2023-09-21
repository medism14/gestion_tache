@extends('layouts.app')

@section('content')

  <div class="card">
    <div class="card-header">
      <div class="row d-flex align-items-center">
          <div class="col-md-3">
            <h3 class="card-title">Archivages de projet</h3>
         </div>  
         
         <div class="col-md-6 d-flex justify-content-center"></div>
        
         
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
      <div class="row">
        <div class="col-md-1"></div>
          <div class="col-md-10">
            <form action="{{ route('projects.archive.search') }}" method="POST">
              @csrf
              <div class="d-md-flex mb-3">
                <select class="form-select text-dark" id="searchSelect" name="searchSelect">
                  <option value="name">Nom du project commençant par : </option>
                  <option value="user">Utilisateur qui est dans le projet : </option>
                  <option value="start_date">date du debut :</option>
                  <option value="status">Statut :</option>
                </select>

                <input type="text" class="form-control" placeholder="Recherche..." name="searchInput" id="searchInputName" required>
                
                <select name="searchInput" id="searchInputUser" class="form-control d-none" disabled>
                  @forelse ($users as $item)
                    <option value="{{ $item->id }}">{{ $item->first_name }} {{ $item->last_name }}</option>
                  @empty
                    <option disabled></option>
                  @endforelse
                </select>

                <select name="searchInput" id="searchInputStatus" class="form-control d-none" disabled>
                	@foreach (\App\Enums\ProjectStatusEnum::cases() as $status)
                		@if ($status->value == 'en_cours')

                		@else
                			<option value="{{ $status->value }}">{{ $status == \App\Enums\ProjectStatusEnum::enCours ? 'En Cours' : ''}} {{ $status == \App\Enums\ProjectStatusEnum::Terminé ? 'Terminé' : ''}} {{ $status == \App\Enums\ProjectStatusEnum::nonAboutti ? 'Non Aboutti' : ''}}</option>
                		@endif
                	@endforeach
                </select>

                <input type="date" class="form-control d-none" placeholder="Recherche..." name="searchInput" id="searchInputStartDate" required disabled>
                <button type="submit" class="btn btn-primary">Rechercher</button>
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
    	<a href="{{ route('projects.index') }}" class="btn btn-secondary float-left"><i class="fas fa-arrow-left"></i> Retour</a>
                  
      <table id="example1" class="table table-bordered table-striped">
        <thead>
          <tr>
           <th></th>
            <th>Nom</th>
            <th>Description</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @php
            $i = ($projects->currentPage() - 1) * $projects->perPage() + 1;
          @endphp

          @forelse($projects as $item)
          	@php
          		if ($item->status == \App\Enums\ProjectStatusEnum::Terminé)
          		{
          			$color = 'bg-success';
          		}

          		if ($item->status == \App\Enums\ProjectStatusEnum::nonAboutti)
          		{
          			$color = 'bg-danger';
          		}
          	@endphp
            <tr>
              <td class="{{ $color }}">{{ $i++ }}</td>
              <td class="id d-none">{{ $item->id }}</td>
              <td class="{{ $color }}">{{ $item->name }}</td>
              <td class="{{ $color }}">{{ $item->description }}</td>
              <td class="{{ $color }}">{{ $item->status == \App\Enums\ProjectStatusEnum::enCours ? 'En Cours' : ''}} {{ $item->status == \App\Enums\ProjectStatusEnum::Terminé ? 'Terminé' : ''}} {{ $item->status == \App\Enums\ProjectStatusEnum::nonAboutti ? 'Non Aboutti' : ''}}</td>
              <td>
                <div class="d-flex justify-content-between align-items-center">
                  <a href="{{ route('projects.show', ['id' => $item->id]) }}" class="text-primary viewProjectBtn mr-2" data-bs-placement="top" title="Voir le projet">
                    <i class="fas fa-eye"></i>
                  </a>
                 
                  <a href="{{ route('projects.destroy',['id' => $item->id]) }}" class="text-danger deleteProjectBtn" 
                  onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ? Cette action est irréversible.');" data-bs-placement="left" title="Supprimer le projet">
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
            <th>Nom</th>
            <th>Description</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

    @if (isset($search))
      <div class="container d-flex justify-content-center align-items-center mt-2">
        <a href="{{ route('projects.archive') }}" class="btn btn-primary">Revenir à la page d'index</a>
      </div>
    @endif




    <!-- Pagination -->
    <div class="pagination d-flex justify-content-center mt-4">
      <ul class="pagination">
        <!-- Lien pour revenir à la première page -->
        @if ($projects->onFirstPage())
          <li class="page-item disabled">
            <span class="page-link">&laquo;</span>
          </li>
        @else
          <li class="page-item">
            <a class="page-link" href="{{ $projects->url(1) }}" aria-label="Première page" data-bs-placement="top" title="Première page" id="first_page">&lt;&lt;</a>
          </li>
          <li class="page-item">
            <a class="page-link" href="{{ $projects->previousPageUrl() }}" rel="prev" data-bs-placement="top" title="Page précedente" id="previous_page">&laquo;</a>
          </li>
        @endif

        @for ($i = 1; $i <= $projects->lastPage(); $i++)
          @if ($i == $projects->currentPage())
            <li class="page-item active">
              <span class="page-link">{{ $i }}</span>
            </li>
          @else
            <li class="page-item">
              <a class="page-link" href="{{ $projects->url($i) }}">{{ $i }}</a>
            </li>
          @endif
        @endfor

        @if ($projects->hasMorePages())
          <li class="page-item">
            <a class="page-link" href="{{ $projects->nextPageUrl() }}" rel="next" data-bs-placement="top" title="Page suivante" id="next_page">&raquo;</a>
          </li>
          <!-- Lien pour revenir à la dernière page -->
        <li class="page-item">
          <a class="page-link" href="{{ $projects->url($projects->lastPage()) }}" aria-label="Dernière page" data-bs-placement="top" title="Dernière page" id="last_page">&gt;&gt;</a>
        </li>
        @else
          <li class="page-item disabled">
            <span class="page-link">&raquo;</span>
          </li>
        @endif
      </ul>
    </div>

@endsection
@section('scripts')
  <script>


    /////////////////////////////////////
    //Afficher le tooltip du view project
    /////////////////////////////////////

    var viewBtns = document.getElementsByClassName('viewProjectBtn');

    for (let btn of viewBtns)
    {
      new bootstrap.Tooltip(btn);
    }

    ///////////////////////////////////////
    //Afficher le tooltip du delete project
    ///////////////////////////////////////

    var deleteBtns = document.getElementsByClassName('deleteProjectBtn');

    for (let btn of deleteBtns)
    {
      new bootstrap.Tooltip(btn);
    }

    ///////////////////////////////////////
    //Pour afficher le modal de edit project
    ///////////////////////////////////////
    
    // Récupérer tous les boutons avec la classe 'editUserBtn'
    const editButtons = document.getElementsByClassName('editProjectBtn');

    // Ajouter un écouteur d'événement à chaque bouton
    for (const button of editButtons) 
    {
      button.addEventListener('click', function (event) {
        let item = event.target;

        
        let id = item.parentNode.parentNode.parentNode.parentNode.children[1].textContent;

        id = parseInt(id);

        fetch('/gestion_projet/get_project_info/' + id)
        .then(response => response.json())
        .then(data => {

          //Clean le modal edit
          clean_edit_select();
          clean_edit_span();
          
          let header_name = document.getElementById('editProjectModalLabel');
          let edit_id= document.getElementById('edit_id');
          let edit_name = document.getElementById('edit_name');
          let edit_description = document.getElementById('edit_description');

          header_name.innerText = 'Modifier le projet ' + data.project.name;
          edit_id.value = data.project.id;
          edit_name.value = data.project.name;
          edit_description.value = data.project.description;

          

          //Remplir le select de l'edit
          for (let userNoGroup of data.usersNoGroup) 
          {
            let selectProjectMembersEdit = document.getElementById('selectProjectMembersEdit');

            //Creation des elements
            let option = document.createElement('option');

            //Modifications des elements
            option.value = userNoGroup.id;
            option.innerText = userNoGroup.first_name + " " + userNoGroup.last_name;


            //Ajouter au select
            selectProjectMembersEdit.appendChild(option);
          } 

          //Remplir l'informations des membres du projet de l'edit
          for (let userOnGroup of data.usersOnGroup)
          {
            //Importer les elements
            let selectProjectMembersEdit = document.getElementById('selectProjectMembersEdit');
            let divProjectMembersEdit = document.getElementById('ProjectMembersEdit');
            let countProjectMembersEdit = document.getElementById('countProjectMembersEdit');

            //Creation des elements
            let span = document.createElement('span');
            let retraitMembre = document.createElement('span');
            let div = document.createElement('div');
            let input = document.createElement('input');

            //incrementation du nombre de membres
            let valueCountProjectMembersEdit = countProjectMembersEdit.value;
            valueCountProjectMembersEdit = parseInt(valueCountProjectMembersEdit) + 1;
            countProjectMembersEdit.value = valueCountProjectMembersEdit;

            //Manipulations des elements
            span.innerText = "-" + userOnGroup.first_name + " " + userOnGroup.last_name;
            retraitMembre.innerText = "❌";
            retraitMembre.classList.add('retraitMembreEdit','ml-1');
            retraitMembre.style.cursor = 'pointer';
            div.classList.add('d-flex');
            input.value = userOnGroup.id;
            input.classList.add('d-none');
            input.name = 'member' + valueCountProjectMembersEdit;

            //Assigner au div row
            div.appendChild(span);
            div.appendChild(retraitMembre);
            div.appendChild(input);

            //mettre le div row dans le grand div
            divProjectMembersEdit.appendChild(div);
          } 
            
            ordonnerAjoutMembreEdit();

          var myModalEdit = new bootstrap.Modal(document.getElementById('editProjectModal'));

          myModalEdit.show();
        })

      })
    }


    /////////////////////////////////////////////////////////////////
    //Pour effacer les messages de validations après un certain temps
    /////////////////////////////////////////////////////////////////

    $(document).ready(function () {
      setTimeout(function () {
        $('#successMessage').fadeOut('slow');
      }, 3000);

      setTimeout (function () {
        $('#deleteMessage').fadeOut('slow');
      }, 3000);
    })

    /////////////////////////////////////////////////////
    //Pour gérer les erreurs pour l'afficher dans l'alert
    /////////////////////////////////////////////////////

    @if ($errors->any())
      let errorMessages = '';
      @foreach ($errors->all() as $error)
        errorMessages += "{{ $error }}" + "\n";
      @endforeach
      alert(errorMessages);
    @endif


    /////////////////////////////////////////////////////
    /////////////////////////////////
    //Manipulation barre de rechreche
    /////////////////////////////////
    /////////////////////////////////////////////////////

    const searchSelect = document.getElementById('searchSelect');

    searchSelect.addEventListener('change', function () {

      let option_selected = searchSelect.options[searchSelect.selectedIndex];
      let searchInputName = document.getElementById('searchInputName');
      let searchInputUser = document.getElementById('searchInputUser');
      let searchInputStartDate = document.getElementById('searchInputStartDate');
      let searchInputStatus = document.getElementById('searchInputStatus');


      switch (option_selected.value) 
      {
        case 'user':
          searchInputUser.classList.remove('d-none');
          searchInputUser.disabled = false;
          searchInputName.classList.add('d-none');
          searchInputName.disabled = true;
          searchInputStartDate.classList.add('d-none');
          searchInputStartDate.disabled = true;
          searchInputStatus.classList.add('d-none');
          searchInputStatus.disabled = true;
          break;
        case 'name':
          searchInputUser.classList.add('d-none');
          searchInputUser.disabled = true
          searchInputName.classList.remove('d-none');
          searchInputName.disabled = false;
          searchInputStartDate.classList.add('d-none');
          searchInputStartDate.disabled = true;
          searchInputStatus.classList.add('d-none');
          searchInputStatus.disabled = true;
          break;
        case 'start_date':
          searchInputUser.classList.add('d-none');
          searchInputUser.disabled = true;
          searchInputName.classList.add('d-none');
          searchInputName.disabled = true;
          searchInputStartDate.classList.remove('d-none');
          searchInputStartDate.disabled = false;
          searchInputStatus.classList.add('d-none');
          searchInputStatus.disabled = true;
          break;
         case 'status':
          searchInputUser.classList.add('d-none');
          searchInputUser.disabled = true;
          searchInputName.classList.add('d-none');
          searchInputName.disabled = true;
          searchInputStartDate.classList.add('d-none');
          searchInputStartDate.disabled = true;
          searchInputStatus.classList.remove('d-none');
          searchInputStatus.disabled = false;
          break;
        default:
          alert('erreur');
      }

    });

    

  </script>
@endsection
