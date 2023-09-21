@extends('layouts.app')

@section('content')

  <div class="card">
    <div class="card-header">
      <div class="row d-flex align-items-center">
          <div class="col-md-3">
            <h3 class="card-title">Gestion de projet</h3>
         </div>  
         
         <div class="col-md-6 d-flex justify-content-md-center justify-content-sm-start mt-md-0 mt-2"><a href="{{ route('projects.archive') }}" class="btn btn-primary" href="#" data-bs-placement="left" title="Archivage de projet" id="archiveBtn">
              Archivage
            </a>
        </div>
        
         <div class="col-md-3 d-flex justify-content-md-end justify-content-sm-start mt-md-0 mt-2">
            <button class="btn btn-primary" href="#" data-bs-placement="left" title="Créer un nouveau projet" id="addProjectBtn">
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
            <form action="{{ route('projects.search') }}" method="POST">
              @csrf
              <div class="d-md-flex mb-3">
                <select class="form-select text-dark" id="searchSelect" name="searchSelect">
                  <option value="name">Nom du project commençant par : </option>
                  <option value="user">Utilisateur qui est dans le projet : </option>
                  <option value="start_date">date du debut :</option>
                </select>

                <input type="text" class="form-control mt-1" placeholder="Recherche..." name="searchInput" id="searchInputName" required>
                
                <select name="searchInput" id="searchInputUser" class="form-control d-none mt-1" disabled>
                  @forelse ($users as $item)
                    <option value="{{ $item->id }}">{{ $item->first_name }} {{ $item->last_name }}</option>
                  @empty
                    <option disabled></option>
                  @endforelse
                </select>

                <input type="date" class="form-control d-none mt-1" placeholder="Recherche..." name="searchInput" id="searchInputStartDate" required disabled>
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
            <tr>
              <td>{{ $i++ }}</td>
              <td class="id d-none">{{ $item->id }}</td>
              <td>{{ $item->name }}</td>
              <td>{{ $item->description }}</td>
              <td>{{ $item->status == \App\Enums\ProjectStatusEnum::enCours ? 'En Cours' : ''}} {{ $item->status == \App\Enums\ProjectStatusEnum::Terminé ? 'Terminé' : ''}} {{ $item->status == \App\Enums\ProjectStatusEnum::nonAboutti ? 'Non Aboutti' : ''}}</td>
              <td>
                <div class="d-flex justify-content-between align-items-center">
                  <a href="{{ route('projects.show', ['id' => $item->id]) }}" class="text-primary viewProjectBtn mr-2" data-bs-placement="top" title="Voir le projet">
                    <i class="fas fa-eye"></i>
                  </a>
                  <button class="text-warning editProjectBtn mr-2 btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier le projet" style="border: none; background: none; padding: 0;">
                      <i class="fas fa-edit"></i>
                  </button>
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
        <a href="{{ route('projects.index') }}" class="btn btn-primary">Revenir à la page d'index</a>
      </div>
    @endif

                    <!---------------------------- Modal ajout projet ---------------------------->
  <div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addProjectModalLabel">Créer un nouveau projet</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('projects.create') }}" id="addProjectForm" onsubmit="return membersProjectExist();">
            @csrf
            
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">  
                  <label for="name">Nom</label>
                  <input type="text" class="form-control" id="name" name="name" required>
                </div>
              </div>
                          
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="description">Description</label><br>
                  <textarea cols="40" rows="4" name="description" id="description"></textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-3">
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                <label for="project_members">Membre du projet</label>
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <select class="form-select" name="project_members" id="selectProjectMembers">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="button" id="addProjectMembers" value="✔" onsubmit="return false" class="btn btn-primary mt-md-0 mt-3">
                        <input type="button" id="addAllProjectMembers" value="Tout" onsubmit="return false" class="btn btn-primary ml-1 mt-md-0 mt-3">
                    </div>
                </div>
              </div>
              </div>
              <div class="col-lg-3"></div>
            </div>
            <div class="row mb-4">
              <div class="col-lg-3"></div>
              <div class="col-lg-6" id="ProjectMembers">
              <input class="d-none" type="number" id="countProjectMembers" name="countProjectMembers" value="0">
                
              </div>
              <div class="col-lg-3"></div>
            </div>
              <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary me-2" id="createProjectBtn">Ajouter</button>
                <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">Annuler</button>
              </div>
          </form>
        </div>
      </div>
    </div>
  </div> 

          <!---------------------------------- Modal edit projet -------------------------------->
  <div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editProjectModalLabel">Modifier le projet </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('projects.update') }}" id="editProjectForm" onsubmit="return membersEditProjectExist();">
            @csrf
            
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group d-none">  
                  <label for="edit_id">ID</label>
                  <input type="text" class="form-control" id="edit_id" name="id" required>
                </div>

                <div class="form-group">  
                  <label for="edit_name">Nom</label>
                  <input type="text" class="form-control" id="edit_name" name="name" required>
                </div>
              </div>
                          
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="edit_description">Description</label><br>
                  <textarea cols="40" rows="4" class="form-control" name="description" id="edit_description"></textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-3"></div>
              <div class="col-lg-6">
                <div class="form-group">  
                  <label for="status">Statut</label>
                  <select id="status" name="status" class="form-select">
                    @foreach (\App\Enums\ProjectStatusEnum::cases() as $status)
                        <option value="{{ $status->value }}">
                            {{ $status->value == 'en_cours' ? 'En cours' : '' }}
                            {{ $status->value == 'non_aboutti' ? 'Non Aboutti' : '' }}
                            {{ $status->value == 'termine' ? 'Terminé' : '' }}
                        </option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-lg-3"></div>
            </div>

            <div class="row">
              <div class="col-lg-3"></div>
              <div class="col-lg-6">
                <div class="form-group">
                <label for="project_membersEdit">Membre du projet</label>
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <select class="form-select" name="project_membersEdit" id="selectProjectMembersEdit">
                                
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="button" id="addProjectMembersEdit" value="✔" onsubmit="return false" class="btn btn-primary">
                    </div>
                </div>
              </div>
              </div>
              <div class="col-lg-3"></div>
            </div>

            <div class="row mb-4">
              <div class="col-lg-3"></div>
              <div class="col-lg-6" id="ProjectMembersEdit">
              <input class="d-none" type="number" id="countProjectMembersEdit" name="countProjectMembersEdit" value="0">
              </div>
              <div class="col-lg-3"></div>
            </div>

            <div class="d-flex justify-content-center">
              <button type="submit" class="btn btn-primary me-2" id="editProjectBtn">Modifier</button>
              <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">Annuler</button>
            </div>
            
          </form>
        </div>
      </div>
    </div>
  </div> 

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

    ////////////////////////////////////
    //Afficher le tooltip du add project
    ////////////////////////////////////
    
    let tooltip_addProjectBtn = new bootstrap.Tooltip(document.getElementById('addProjectBtn'));

    ////////////////////////////////////
    //Afficher le tooltip du add project
    ////////////////////////////////////

    const tooltip_achivageBtn = new bootstrap.Tooltip(document.getElementById('archiveBtn'));

    /////////////////////////////////////
    //Afficher le tooltip du view project
    /////////////////////////////////////

    let viewBtns = document.getElementsByClassName('viewProjectBtn');

    for (let btn of viewBtns)
    {
      new bootstrap.Tooltip(btn);
    }

    /////////////////////////////////////
    //Afficher le tooltip du edit project
    /////////////////////////////////////

    let editBtns = document.getElementsByClassName('editProjectBtn');

    for (let btn of editBtns)
    {
      new bootstrap.Tooltip(btn);
    }

    ///////////////////////////////////////
    //Afficher le tooltip du delete project
    ///////////////////////////////////////

    let deleteBtns = document.getElementsByClassName('deleteProjectBtn');

    for (let btn of deleteBtns)
    {
      new bootstrap.Tooltip(btn);
    }

    ///////////////////////////////////////
    //Pour afficher le modal de add project
    ///////////////////////////////////////

    let addProjectBtn =document.getElementById('addProjectBtn') 
    addProjectBtn.addEventListener('click', function (event){
      let addProjectModal = new bootstrap.Modal(document.getElementById('addProjectModal'));

      addProjectBtn.disabled = true;

            setTimeout(function () {
                addProjectBtn.disabled = false;
            }, 1000);

      addProjectModal.show();
    });

    ///////////////////////////////////////
    //Pour afficher le modal de edit project
    ///////////////////////////////////////
    
    // Récupérer tous les boutons avec la classe 'editUserBtn'
    const editButtons = document.getElementsByClassName('editProjectBtn');

    // Ajouter un écouteur d'événement à chaque bouton
    for (const button of editButtons) 
    {
      button.addEventListener('click', function (event) {

        button.disabled = true;

        setTimeout(function () {
            button.disabled = false;
        }, 1000);

        let id = event.target.parentNode.parentNode.parentNode;

        if (id.tagName === 'TR')
        {
          id = id.children[1].textContent;
        }
        else
        {
          id = id.parentNode.children[1].textContent;
        } 

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

          console.log(data.usersNoGroup)

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

          let myModalEdit = new bootstrap.Modal(document.getElementById('editProjectModal'));

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

    //////////////////////////////////////////////////////
    ///////////////////////////
    //Manipulation Ajout membre
    ///////////////////////////
    //////////////////////////////////////////////////////

    //Ajouter tout les membres
    document.getElementById('addAllProjectMembers').addEventListener('click', function (event) {
        //Récuperation du count, du select et du div ou l'on mettra les infos
      let selectProjectMembers = document.getElementById('selectProjectMembers');
      let divProjectMembers = document.getElementById('ProjectMembers');

      //Si le select est vide
      if (selectProjectMembers.options.length === 0)
      {
        alert('Il n\'y a plus d\'options disponibles');
        return;
      }

      for (let user of selectProjectMembers.children)
      {
        //Récuperer l'id et le name du select
        let id = user.value;
        let name = user.text;

        //Creation du div
        let div = document.createElement('div');
        div.classList.add('d-flex');

        //Création des éléments
        let span = document.createElement('span');
        let input = document.createElement('input');
        let retraitMembre = document.createElement('span');

        //Manipulation des éléments
        span.innerText = "-" + name;
        input.value = id;
        input.classList.add('d-none');
        retraitMembre.innerText = "❌";
        retraitMembre.classList.add('retraitMembre','ml-1');
        retraitMembre.style.cursor = 'pointer';

        //mettre l'input et le span dans le div avec le row
        div.appendChild(span);
        div.appendChild(retraitMembre);
        div.appendChild(input);

        //mettre le div row dans le grand div
        divProjectMembers.appendChild(div);

      }

      selectProjectMembers.options.length = 0;

      ordonnerAjoutMembre();

    });

    //Ajouter un seul membre
    document.getElementById('addProjectMembers').addEventListener('click', function (event) {
      
      //Récuperation du count, du select et du div ou l'on mettra les infos
      let selectProjectMembers = document.getElementById('selectProjectMembers');
      let divProjectMembers = document.getElementById('ProjectMembers');

      //Si le select est vide
      if (selectProjectMembers.options.length === 0)
      {
        alert('Il n\'y a plus d\'options disponibles');
        return;
      }


      //Récuperer l'id et le name du select
      let id = selectProjectMembers.value;
      let name = selectProjectMembers.options[selectProjectMembers.selectedIndex].text;

      //Creation du div
      let div = document.createElement('div');
      div.classList.add('d-flex');

      //Création des éléments
      let span = document.createElement('span');
      let input = document.createElement('input');
      let retraitMembre = document.createElement('span');

      //Manipulation des éléments
      span.innerText = "-" + name;
      input.value = id;
      input.classList.add('d-none');
      retraitMembre.innerText = "❌";
      retraitMembre.classList.add('retraitMembre','ml-1');
      retraitMembre.style.cursor = 'pointer';

      //mettre l'input et le span dans le div avec le row
      div.appendChild(span);
      div.appendChild(retraitMembre);
      div.appendChild(input);

      //mettre le div row dans le grand div
      divProjectMembers.appendChild(div);

      //Enlever le truc selectionné de l'option
      let option_selected = selectProjectMembers.options[selectProjectMembers.selectedIndex];
      option_selected.remove();

      ordonnerAjoutMembre();
    });

    ////////////////
    //Retrait membre
    ////////////////

      divProjectMembers = document.getElementById('ProjectMembers');

        divProjectMembers.addEventListener('click', function (event) {

          let item = event.target;

          if (item.classList.contains('retraitMembre'))
          {
            //recuperer le id et le nom
            let id = parseInt(item.parentNode.children[2].value);
            let name = item.parentNode.children[0].textContent;
            name = name.substring(1);

            //creation de l'option
            let option = document.createElement('option');
            option.value = id;
            option.innerText = name;
            selectProjectMembers.appendChild(option);

            //Suppression de la ligne
            let div = item.parentNode;
            div.remove();

            ordonnerAjoutMembre();
          }
        });
    

    ///////////////////////////////////////////////////
    //Verification si au moins un membre dans un projet
    ///////////////////////////////////////////////////

    function membersProjectExist() 
    {
      let divProjectMembers = document.getElementById('ProjectMembers');

      if (divProjectMembers.children.length > 1) 
      {
        return true;
      } else 
        {
          // Annuler l'envoi du formulaire
          alert('Il faut au moins relier le projet à un membre');
          return false;
        }
    }

    ////////////////////////////////
    //Ordonner les membres du groupe
    ////////////////////////////////

    function ordonnerAjoutMembre () 
    {
      //Importation des elements
      let divProjectMembers = document.getElementById('ProjectMembers');
      let countProjectMembers = document.getElementById('countProjectMembers');

      //Comptage des membres
      let countMembers = divProjectMembers.children.length - 1;
      countProjectMembers.value = countMembers;

      //Attribution nom aux membres
      let childrens = divProjectMembers.children;

      let i = 0
      for (let children of childrens)
      {
        if (i == 0)
        {
          //Pour pas mettre le countProjetMembers
          i++;
        } 
        else 
        {
          //recuperation de l'input pour changer le nom
          input = children.children[2];
          input.name = 'member' + i;
          i++;
        }
      }
    }

    //////////////////////////////////////////////////////
    //////////////////////////////////
    //Manipulation Modification membre
    //////////////////////////////////
    //////////////////////////////////////////////////////

    document.getElementById('addProjectMembersEdit').addEventListener('click', function (event) {
      
      //Récuperation du count, du select et du div ou l'on mettra les infos
      let selectProjectMembersEdit = document.getElementById('selectProjectMembersEdit');
      let divProjectMembersEdit = document.getElementById('ProjectMembersEdit');
      let countProjectMembersEdit = document.getElementById('countProjectMembersEdit');

      //Si le select est vide
      if (selectProjectMembersEdit.options.length === 0)
      {
        alert('Il n\'y a plus d\'options disponibles');
        return;
      }

      //Récuperer l'id et le name du select
      let id = selectProjectMembersEdit.value;
      let name = selectProjectMembersEdit.options[selectProjectMembersEdit.selectedIndex].text;

      //Creation du div
      let div = document.createElement('div');
      div.classList.add('d-flex');

      //Création des éléments
      let span = document.createElement('span');
      let input = document.createElement('input');
      let retraitMembre = document.createElement('span');

      //Manipulation des éléments
      span.innerText = "-" + name;
      input.value = id;
      input.classList.add('d-none');
      retraitMembre.innerText = "❌";
      retraitMembre.classList.add('retraitMembreEdit','ml-1');
      retraitMembre.style.cursor = 'pointer';

      //mettre l'input et le span dans le div avec le row
      div.appendChild(span);
      div.appendChild(retraitMembre);
      div.appendChild(input);

      //mettre le div row dans le grand div
      divProjectMembersEdit.appendChild(div);

      //Enlever le truc selectionné de l'option
      let option_selected = selectProjectMembersEdit.options[selectProjectMembersEdit.selectedIndex];
      option_selected.remove();

      ordonnerAjoutMembreEdit();
    });

    ////////////////
    //Retrait membre
    ////////////////

      divProjectMembersEdit = document.getElementById('ProjectMembersEdit');

        divProjectMembersEdit.addEventListener('click', function (event) {

          let item = event.target;

          if (item.classList.contains('retraitMembreEdit'))
          {
            //recuperer le id et le nom
            let id = parseInt(item.parentNode.children[2].value);
            let name = item.parentNode.children[0].textContent;
            name = name.substring(1);

            //creation de l'option
            let option = document.createElement('option');
            option.value = id;
            option.innerText = name;
            selectProjectMembersEdit.appendChild(option);

            //Suppression de la ligne
            let div = item.parentNode;
            div.remove();

            ordonnerAjoutMembreEdit();
          }
        });
    

    ///////////////////////////////////////////////////
    //Verification si au moins un membre dans un projet
    ///////////////////////////////////////////////////

    function membersEditProjectExist() 
    {
      let divProjectMembersEdit = document.getElementById('ProjectMembersEdit');

      if (divProjectMembersEdit.children.length > 1) 
      {
        return true;
      } else 
        {
          // Annuler l'envoi du formulaire
          alert('Il faut au moins relier le projet à un membre');
          return false;
        }
    }

    ////////////////////////////////
    //Ordonner les membres du groupe
    ////////////////////////////////

     function ordonnerAjoutMembreEdit () 
    {
      //Importation des elements
      let divProjectMembersEdit = document.getElementById('ProjectMembersEdit');
      let countProjectMembersEdit = document.getElementById('countProjectMembersEdit');

      //Comptage des membres
      let countMembersEdit = divProjectMembersEdit.children.length - 1;
      countProjectMembersEdit.value = countMembersEdit;

      //Attribution nom aux membres
      let childrens = divProjectMembersEdit.children;

      let i = 0
      for (let children of childrens)
      {
        if (i == 0)
        {
          //Pour pas mettre le countProjetMembersEdit
          i++;
        } 
        else 
        {
          //recuperation de l'input pour changer le nom
          input = children.children[2];
          input.name = 'member' + i;
          i++;
        }
      }
    }

    ////////////////////////////
    //Rendre clean le modal edit
    ////////////////////////////

    function clean_edit_select ()
    {
      let selectProjectMembersEdit = document.getElementById('selectProjectMembersEdit');
    
      selectProjectMembersEdit.innerHTML = '';
    }

    function clean_edit_span() {
      let divProjectMembersEdit = document.getElementById('ProjectMembersEdit');

      for (let i = divProjectMembersEdit.children.length - 1; i > 0; i--) {
        divProjectMembersEdit.removeChild(divProjectMembersEdit.children[i]);
      }

    }

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


      switch (option_selected.value) 
      {
        case 'user':
          searchInputUser.classList.remove('d-none');
          searchInputUser.disabled = false;
          searchInputName.classList.add('d-none');
          searchInputName.disabled = true;
          searchInputStartDate.classList.add('d-none');
          searchInputStartDate.disabled = true;
          break;
        case 'name':
          searchInputUser.classList.add('d-none');
          searchInputUser.disabled = true
          searchInputName.classList.remove('d-none');
          searchInputName.disabled = false;
          searchInputStartDate.classList.add('d-none');
          searchInputStartDate.disabled = true;
          break;
        case 'start_date':
          searchInputUser.classList.add('d-none');
          searchInputUser.disabled = true;
          searchInputName.classList.add('d-none');
          searchInputName.disabled = true;
          searchInputStartDate.classList.remove('d-none');
          searchInputStartDate.disabled = false;
          break;
        default:
          alert('erreur');
      }

    });

  ////////////////////////////////////////////////////////////////////////////////////////////
  //Pour annuler l'envoie plusieurs fois//////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////

    let avoidForm = document.getElementById('addProjectForm'); // Nom du formulaire d'ajout
  
    avoidForm.addEventListener('submit', function () {

    let avoidBtn = document.getElementById('createProjectBtn'); // Nom du bouton d'ajout

    avoidBtn.disabled = true;

    setTimeout(function () {
        avoidBtn.disabled = false;
    }, 3000);
    });

  </script>
@endsection
