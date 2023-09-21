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
                      <h3 class="card-title">Gestion de tâches</h3>
                    </div>  
                    <div class="col-md-6 mt-md-0 mt-sm-2">
                        <div class="d-block d-flex justify-content-center"><b><u>Phase : {{ $phase->name }}</u></b></div>
                        <div class="d-block d-flex justify-content-center"><b><u>Projet : {{ $project->name }}</u></b></div>
                    </div>
                    <div class="col-md-3 d-flex justify-content-md-end justify-content-sm-start mt-md-0 mt-sm-2">

                      @if ($project->status == \App\Enums\ProjectStatusEnum::enCours)
                        <button class="btn btn-primary" href="#" data-bs-placement="left" title="Créer une nouvelle tâche" id="addTaskBtn">
                            <i class="fas fa-plus"></i>
                          </button>
                      @else

                      @endif
                          
                    </div>
                </div>
              </div>

              <!-- /.card-header -->
              <div class="card-body">
                <div class="row">
                  <div class="col-md-2">
                    <a href="{{ route('projects.show', [
                    'id' => $project->id
                    ]) }}" class="btn btn-secondary float-start mb-md-0 mb-1"><i class="fas fa-arrow-left"></i> Retour</a>
                  </div>
                  <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10">

                          <form action="{{ route('projects.tasks.search', [
                            'id' => $project->id,
                            'phase' => $phase->name,
                            'phase_id' => $phase->id,
                          ]) }}" method="POST">
                            @csrf
                            <div class="d-md-flex">
                              <select class="form-select text-dark mb-md-0 mb-1" id="filterSelect" name="filter">
                                <option value="start_date">Date de début</option>
                                <option value="end_date">Date de fin</option>
                                <option value="status">Statut</option>
                                <option value="name">Nom qui commence par...</option>
                              </select>
                                <input type="date" class="form-control mt-md-0 mt-sm-2 mb-md-0 mb-1" placeholder="Recherche..." name="search" id="searchInputStartDate" required>

                                <input type="date" class="form-control mt-md-0 mt-sm-2 d-none" placeholder="Recherche..." name="search" id="searchInputEndDate" required disabled>

                                <select name="search" class="form-select d-none" id="searchInputStatus" disabled>
                                  @foreach (\App\Enums\TaskStatusEnum::cases() as $status)
                                    <option value="{{ $status->value }}">{{ $status->name == 'enCours' ? 'En Cours' : ''}} {{ $status->name == 'enAttente' ? 'En Attente' : ''}}  {{ $status->name == 'nonAboutti' ? 'Non Aboutti' : ''}} {{ $status->name == 'Terminé' ? 'Terminé' : ''}} {{ $status->name == 'Expiré' ? 'Expiré' : ''}}</option>
                                  @endforeach
                                </select>

                                <input type="text" class="form-control mt-md-0 mt-sm-2 d-none" placeholder="Recherche..." name="search" id="searchInputName" required disabled>
                                  <button type="submit" class="btn btn-primary mt-md-0 mt-sm-2">Rechercher</button>
                            </div>
                          </form>
                          </div>
                          <div class="col-md-1"></div>
                        </div>
                  </div>
                  <div class="col-md-2"></div>
                </div>
                
                     
                        <hr class="bg-black">
                        @if (isset($search))

                        @else
                        <div class="row">
                              <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                              <input type="radio" class="btn-check task_option" name="task_option" id="past" autocomplete="off" {{ session('active_tab') == 'past'  ? 'checked' : ''}} value="past">
                              <label class="btn btn-outline-primary" for="past">Tâches passés</label>

                              <input type="radio" class="btn-check task_option" name="task_option" id="today" autocomplete="off" {{ session('active_tab') == 'today'  ? 'checked' : ''}} value="today">
                              <label class="btn btn-outline-primary" for="today">Tâches d'aujourd'hui</label>

                              <input type="radio" class="btn-check task_option" name="task_option" id="future" autocomplete="off" {{ session('active_tab') == 'future'  ? 'checked' : ''}} value="future">
                              <label class="btn btn-outline-primary" for="future">Tâches futures</label>
                            </div>
                        </div>
                      @endif  

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

<!-- Table aujourd'hui -->
<table id="tableToday" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th></th>
      <th>Nom</th>
      <th>Statut</th>
      <th>Attribué à </th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
      @php
        $i = ($tasks->currentPage() - 1) * $tasks->perPage() + 1;
      @endphp
      @forelse($tasks as $item)
      @php
        $currentDate = now()->addhours(3);
        $endDate = \Carbon\Carbon::parse($item->end_date);
        $startDate = \Carbon\Carbon::parse($item->start_date);
        $intervalEnCours = $currentDate->diff($endDate);
        $intervalEnAttente = $currentDate->diff($startDate);
        $tempsRestantEnCours = $intervalEnCours->format('%a jours');
        $tempsRestantEnAttente = $intervalEnAttente->format('%a jours');

         $taskStatusClass = '';

          switch ($item->status) {
              case \App\Enums\TaskStatusEnum::enAttente:
                  $taskStatusClass = 'bg-warning';
                  break;
              case \App\Enums\TaskStatusEnum::enCours:
                  $taskStatusClass = 'bg-success';
                  break;
              case \App\Enums\TaskStatusEnum::Terminé:
                  $taskStatusClass = 'bg-secondary';
                  break;
              case \App\Enums\TaskStatusEnum::Expiré:
                  $taskStatusClass = 'bg-danger';
                  break;
              case \App\Enums\TaskStatusEnum::nonAboutti:
                  $taskStatusClass = 'bg-danger';
                  break;
          }
      @endphp


        <tr>
          <td class="{{ $taskStatusClass }}">{{ $i++ }}</td>
          <td class="id d-none {{ $taskStatusClass }}">{{ $item->id }}</td>
          <td class="{{ $taskStatusClass }}">{{ $item->name }}</td>
          <td class="{{ $taskStatusClass }}">
            @switch ($item->status)
              @case (\App\Enums\TaskStatusEnum::enAttente)
                En Attente (Prévu Dans : {{ $tempsRestantEnAttente }})
                @break
              @case (\App\Enums\TaskStatusEnum::enCours)
                En Cours (Temps restant : {{ $tempsRestantEnCours }})
                @break
              @case (\App\Enums\TaskStatusEnum::nonAboutti)
                Non Aboutti
                @break
              @case (\App\Enums\TaskStatusEnum::Terminé)
                Terminé
                @break
              @case (\App\Enums\TaskStatusEnum::Expiré)
                Expiré
                @break
            @endswitch
          </td>
          <td class="{{ $taskStatusClass }}">{{ $item->user->first_name }} {{ $item->user->last_name }}</td>
          <td>
            @if (($project->status == \App\Enums\ProjectStatusEnum::enCours))
              <div class="d-flex justify-content-between align-items-center">
              <button href="#" class="text-primary viewTaskBtn mr-2 btn" onclick="return false;" data-bs-placement="top" title="Voir la tâche">
                <i class="fas fa-eye"></i>
              </button>
              <button href="#" class="text-warning editTaskBtn mr-2 btn" onclick="return false;" data-bs-placement="top" title="Modifier la tâche">
                <i class="fas fa-edit"></i>
              </button>
              <a href="{{ route('projects.tasks.delete',[
                'id' => $project->id,
                'phase' => $phase->name,
                'phase_id' => $phase->id,
                'task_id' => $item->id,
              ]) }}" class="text-danger deleteTaskBtn btn" 
                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ? Cette action est irréversible.');" data-bs-placement="left" title="Supprimer la tâche">
                <i class="fas fa-trash-alt"></i>
              </a>
            </div>
            @else
              <div class="d-flex justify-content-center align-items-center">
              <a href="#" class="text-primary viewTaskBtn mr-2" onclick="return false;" data-bs-placement="top" title="Voir la tâche">
                <i class="fas fa-eye"></i>
              </a>
            </div>
            @endif
            
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
      <th>Statut</th>
      <th>Attribué à</th>
      <th>Actions</th>
    </tr>
  </tfoot>
</table>

  @if (isset($search))
    <div class="container d-flex justify-content-center align-items-center mt-4">
    <a href="{{ route('projects.tasks.index', [
      'id' => $project->id,
      'phase' => $phase->name,
      'phase_id' => $phase->id,
    ]) }}" class="btn btn-primary">Revenir à la page d'index</a>
  </div>
  @endif

<!-- Pagination -->
    <div class="pagination d-flex justify-content-center mt-4">
      <ul class="pagination">
        <!-- Lien pour revenir à la première page -->
        @if ($tasks->onFirstPage())
          <li class="page-item disabled">
            <span class="page-link">&laquo;</span>
          </li>
        @else
          <li class="page-item">
            <a class="page-link" href="{{ $tasks->url(1) }}" aria-label="Première page" data-bs-placement="top" title="Première page" id="first_page">&lt;&lt;</a>
          </li>
          <li class="page-item">
            <a class="page-link" href="{{ $tasks->previousPageUrl() }}" rel="prev" data-bs-placement="top" title="Page précedente" id="previous_page">&laquo;</a>
          </li>
        @endif

        @for ($i = 1; $i <= $tasks->lastPage(); $i++)
          @if ($i == $tasks->currentPage())
            <li class="page-item active">
              <span class="page-link">{{ $i }}</span>
            </li>
          @else
            <li class="page-item">
              <a class="page-link" href="{{ $tasks->url($i) }}">{{ $i }}</a>
            </li>
          @endif
        @endfor

        @if ($tasks->hasMorePages())
          <li class="page-item">
            <a class="page-link" href="{{ $tasks->nextPageUrl() }}" rel="next" data-bs-placement="top" title="Page suivante" id="next_page">&raquo;</a>
          </li>
          <!-- Lien pour revenir à la dernière page -->
        <li class="page-item">
          <a class="page-link" href="{{ $tasks->url($tasks->lastPage()) }}" aria-label="Dernière page" data-bs-placement="top" title="Dernière page" id="last_page">&gt;&gt;</a>
        </li>
        @else
          <li class="page-item disabled">
            <span class="page-link">&raquo;</span>
          </li>
        @endif
      </ul>
    </div>



                <!---------------------------- Modal ajout tache ---------------------------->
          <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="addTaskModalLabel">Créer une nouvelle tâche</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form method="POST" action="{{ route('projects.tasks.create', [
                      'id' => $project->id,
                      'phase' => $phase->name,
                      'phase_id' => $phase->id,
                  ]) }}" id="addTaskForm" enctype="multipart/form-data">
                    @csrf
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">  
                              <label for="name">Nom</label>
                              <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                              <label for="description">Description</label><br>
                              <textarea rows="3" cols="50" class="form-control" name="description" id="description"></textarea>
                            </div>
                          </div>

                          <div class="col-lg-6">
                            <div class="form-group">
                              <label for="start_date">dateDebut</label>
                              <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="form-group">
                              <label for="end_date">dateFin</label>
                              <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-3"></div>
                          <div class="col-lg-6">
                            <div class="form-group">
                              <label for="user">Utilisateur</label>
                              <select name="user" id="user" class="form-select">
                                @forelse ($users as $user)
                                  <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                @empty
                                  <option disabled>Aucun utilsateur en lice</option>
                                @endforelse
                              </select>
                            </div>
                          </div>
                          <div class="col-lg-3"></div>
                        </div>
                      <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary me-2" id="createTaskBtn">Ajouter</button>
                        <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">Annuler</button>
                      </div>
                  </div>
                </form>
                </div>
              </div>
            </div>
          </div> 
          </div> 


          <!---------------------------------- Modal vue tache -------------------------------->
          <div class="modal" id="viewTaskModal" tabindex="-1" aria-labelledby="viewTaskModalLabel" aria-hadden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewTaskModalLabel">Voir une tâche</h5>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
                </div>
                <div class="modal-body">
                  <form method="POST">
                    @csrf
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">  
                              <label for="view_name">Nom</label>
                              <input type="text" class="form-control" id="view_name" name="view_name" disabled required>
                            </div>
                            <div class="form-group">
                              <label for="view_description">Description</label><br>
                              <textarea rows="3" cols="50" name="view_description" id="view_description" disabled></textarea>
                            </div>
                            
                          </div>

                            <div class="col-lg-6">
                              <div class="form-group">
                                <label for="view_start_date">dateDebut</label>
                                <input type="datetime-local" class="form-control" id="view_start_date" name="view_start_date" required disabled>
                              </div>

                              <div class="form-group">
                                 <label for="end_date">dateFin</label>
                                 <input type="datetime-local" class="form-control" id="view_end_date" name="view_end_date" required disabled>
                               </div>
                            </div>
                          </div>

                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group">  
                              <label for="view_status" class="mr-2">Statut :</label>
                              <input type="text" class="form-control" id="view_status" name="view_status" disabled required>
                            </div>
                          </div>

                            <div class="col-lg-6">
                              <div class="form-group">
                                <label for="view_user">Utilisateur</label><br>
                                <input type="text" class="form-control" id="view_user" name="view_user" disabled required>
                              </div>
                            </div>
                        </div>

                        <div class="row d-none" id="confirmed">
                          <div class="col-lg-6">
                            <div class="form-group">  
                              <label for="view_confirmation_date" class="mr-2">Date de confirmation :</label>
                              <input type="text" class="form-control" id="view_confirmation_date" name="view_confirmation_date" disabled required>
                            </div>
                          </div>

                            <div class="col-lg-6">
                              <div class="form-group">
                                <label for="view_comment">Commentaire</label><br>
                              <textarea rows="3" cols="50" name="view_comment" id="view_comment" disabled></textarea>
                              </div>
                            </div>
                        </div>
                      </form>
              </div>
            </div>
          </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          <!---------------------------------- Modal edit utilisateur -------------------------------->

                        <!-- Bouton pour ouvrir le modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editTaskModalLabel">Modifier la tâche</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form method="POST" action="{{ route('projects.tasks.update', [
                      'id' => $project->id,
                      'phase' => $phase->name,
                      'phase_id' => $phase->id,
                  ]) }}" id="editTaskForm">
                    @csrf
                        <div class="row">
                          <div class="col-lg-6">
                            <div class="form-group d-none">  
                              <label for="first_name">ID</label>
                              <input type="text" class="form-control" id="edit_id" name="id" required>
                            </div>
                            <div class="form-group">  
                              <label for="name">Nom</label>
                              <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                            <div class="form-group">
                              <label for="description">Description</label><br>
                              <textarea rows="3" cols="50" name="description" id="edit_description"></textarea>
                            </div>
                            
                          </div>
                            <div class="col-lg-6">
                            
                            <div class="form-group">
                              <label for="start_date">dateDebut</label>
                              <input type="datetime-local" class="form-control" id="edit_start_date" name="start_date" required>
                            </div>

                            <div class="form-group">
                                <label for="end_date">dateFin</label>
                                <input type="datetime-local" class="form-control" id="edit_end_date" name="end_date" required>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-lg-6">
                              <div class="form-group">  
                                <label for="status" class="mr-2">Statut :</label>
                                  <select name="status" id="edit_status" class="form-select">
                                    
                                  </select>
                              </div>
                            </div>
                            <div class="col-lg-6">
                              <div class="form-group">
                                <div class="form-group">
                                  <label for="user">Utilisateur</label><br>
                                  <select name="user_id" id="edit_user" class="form-select">
                                    
                                  </select>
                                </div>
                              </div>
                            </div>
                          </div>
                      <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary me-2" id="editTaskBtn">Modifier</button>
                        <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">Annuler</button>
                      </div>
                  </div>
                </form>
                </div>
              </div>
            </div>
          </div> 
          </div> 

@endsection

@section('scripts')
    <script>

document.addEventListener('DOMContentLoaded', function() {
        // Activer le tooltip

        @if (($project->status == \App\Enums\ProjectStatusEnum::enCours))
          let tooltip_addtaskBtn = new bootstrap.Tooltip(document.getElementById('addTaskBtn'));

          let editTaskBtns = document.querySelectorAll('.editTaskBtn');
          editTaskBtns.forEach(function(btn) {
              new bootstrap.Tooltip(btn);
          });

          let deleteTaskBtns = document.querySelectorAll('.deleteTaskBtn');
          deleteTaskBtns.forEach(function(btn) {
              new bootstrap.Tooltip(btn);
          });
        @else

        @endif
        

        let viewTaskBtns = document.querySelectorAll('.viewTaskBtn');
        viewTaskBtns.forEach(function(btn) {
            new bootstrap.Tooltip(btn);
        });

        

        // Vérifie si l'élément avec l'ID 'first_page' existe
        let firstPageElement = document.getElementById('first_page');
        if (firstPageElement) {
          let tooltip_first_page = new bootstrap.Tooltip(firstPageElement);
        }

        // Vérifie si l'élément avec l'ID 'previous_page' existe
        let previousPageElement = document.getElementById('previous_page');
        if (previousPageElement) {
          let tooltip_previous_page = new bootstrap.Tooltip(previousPageElement);
        }

        // Vérifie si l'élément avec l'ID 'next_page' existe
        let nextPageElement = document.getElementById('next_page');
        if (nextPageElement) {
          let tooltip_next_page = new bootstrap.Tooltip(nextPageElement);
        }

        // Vérifie si l'élément avec l'ID 'last_page' existe
        let lastPageElement = document.getElementById('last_page');
        if (lastPageElement) {
          let tooltip_last_page = new bootstrap.Tooltip(lastPageElement);
        }




        });
        
      @if (($project->status == \App\Enums\ProjectStatusEnum::enCours))
  /////////////////////////////////
  //EDIT TASK//////////////////////
  /////////////////////////////////

// Récupérer tous les boutons avec la classe 'editUserBtn'
const editButtons = document.getElementsByClassName('editTaskBtn');

// Ajouter un écouteur d'événement à chaque bouton
  for (const button of editButtons) {
  button.addEventListener('click', function(event) {
    button.disabled = true;

    setTimeout(function () {
        button.disabled = false;
    }, 1000);

    let tr = event.target.parentNode.parentNode.parentNode.parentNode;
    let task_id = tr.children[1].textContent;

  task_id = parseInt(task_id);    
    // Envoi de la requête AJAX
    fetch('/gestion_projet/show_project/{{ $project->id }}/{{ $phase }}/{{ $phase->id }}/tasks/get_task_info/' + task_id)
      .then(response => response.json())
      .then(data => {

          clean_edit();

          //Recuperation des champs
          let edit_id = document.getElementById('edit_id'); 
          let name = document.getElementById('edit_name');
          let description = document.getElementById('edit_description');
          let dateFin = document.getElementById('edit_end_date');
          let dateDebut = document.getElementById('edit_start_date');
          let status = document.getElementById('edit_status');
          let userSelect = document.getElementById('edit_user');

          edit_id.value = task_id;
          name.value = data.task.name;
          description.value = data.task.description;
          dateFin.value = data.task.end_date;
          dateDebut.value = data.task.start_date;

          //Partie pour le status
          let optionEnAttente = document.createElement('option');          
          let optionEnCours = document.createElement('option');          
          let optionTermine = document.createElement('option');          
          let optionExpire = document.createElement('option');

          optionEnAttente.value = 'en_attente';
          optionEnAttente.innerText = 'En Attente';

          optionEnCours.value = 'en_cours';
          optionEnCours.innerText = 'En Cours';

          optionTermine.value = 'termine';
          optionTermine.innerText = 'Terminé';

          optionExpire.value = 'expire';
          optionExpire.innerText = 'Expiré';

          switch (data.task.status)
          {
            case 'en_attente':
              optionEnAttente.selected = true;
              optionEnCours.selected = false;
              optionTermine.selected = false;
              optionExpire.selected = false;
              break;
            case 'en_cours':
              optionEnAttente.selected = false;
              optionEnCours.selected = true;
              optionTermine.selected = false;
              optionExpire.selected = false;
              break;
            case 'termine':
              optionEnAttente.selected = false;
              optionEnCours.selected = false;
              optionTermine.selected = true;
              optionExpire.selected = false;
              break;
            case 'expire':
              optionEnAttente.selected = false;
              optionEnCours.selected = false;
              optionTermine.selected = false;
              optionExpire.selected = true;
              break;
          } 

          status.appendChild(optionEnAttente);
          status.appendChild(optionEnCours);
          status.appendChild(optionTermine);
          status.appendChild(optionExpire);

          //partie pour l'utilisateur
          for (let user of data.users)
          {
            let optionUser = document.createElement('option');
            optionUser.value = user.id;
            optionUser.innerText = user.first_name + ' ' + user.last_name;
              //Comparaison entre l'utilisateur du task et celui dans la boucle
              if (data.user.id == user.id)
              {
                optionUser.selected = true;
              }
              userSelect.appendChild(optionUser);
          }


        // Ouvrir le modal une fois les données mises à jour
        let myModal_edit = new bootstrap.Modal(document.getElementById('editTaskModal'));
        myModal_edit.show();
      })
      .catch(error => {
        console.error(error);
      });
  });
} 

  

      /////////////////////////////////
  //ADD TASK///////////////////////
  /////////////////////////////////

// Fonction pour ouvrir le modal lorsqu'on clique sur le bouton "Ajouter utilisateur"
        let addTaskBtn = document.getElementById('addTaskBtn');
        addTaskBtn.addEventListener('click', function() {
          addTaskBtn.disabled = true;

          setTimeout(function () {
              addTaskBtn.disabled = false;
          }, 1000);
          let myModal_add = new bootstrap.Modal(document.getElementById('addTaskModal'));
          myModal_add.show();
        });

//Verification de mot de passe
  
    @if ($errors->any())
    // Récupérer les messages d'erreur du tableau d'erreurs PHP et les afficher dans une alerte JavaScript
    let errorMessage = '';
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
    // Cacher le message de succès après 3 secondes
    setTimeout(function() {
        $('#deleteMessage').fadeOut('slow');
      }, 3000);
    });

 


      @else

      @endif

     //MODIFICATION BARRE DE RECHERCHE
  const filterSelect = document.getElementById('filterSelect');
  const searchInputStartDate = document.getElementById("searchInputStartDate");
  const searchInputEndDate = document.getElementById('searchInputEndDate');
  const searchInputStatus = document.getElementById('searchInputStatus');
  const searchInputName = document.getElementById('searchInputName');

  filterSelect.addEventListener('change', function (event) {
    let item = event.target;

    searchInputStartDate.value = '';
    searchInputEndDate.value = '';
    searchInputName.value = '';

    switch(filterSelect.value) {

    case "start_date":
      searchInputStartDate.disabled = false;
      searchInputEndDate.disabled = true;
      searchInputStatus.disabled = true;
      searchInputName.disabled = true;

      searchInputStartDate.classList.remove('d-none');
      searchInputEndDate.classList.add('d-none');
      searchInputStatus.classList.add('d-none');
      searchInputName.classList.add('d-none');

      break;
    case "end_date":
      searchInputStartDate.disabled = true;
      searchInputEndDate.disabled = false;
      searchInputStatus.disabled = true;
      searchInputName.disabled = true;

      searchInputStartDate.classList.add('d-none');
      searchInputEndDate.classList.remove('d-none');
      searchInputStatus.classList.add('d-none');
      searchInputName.classList.add('d-none');

      break;
    case "status":
      searchInputStartDate.disabled = true;
      searchInputEndDate.disabled = true;
      searchInputStatus.disabled = false;
      searchInputName.disabled = true;

      searchInputStartDate.classList.add('d-none');
      searchInputEndDate.classList.add('d-none');
      searchInputStatus.classList.remove('d-none');
      searchInputName.classList.add('d-none');

      break;
    case "name":
      searchInputStartDate.disabled = true;
      searchInputEndDate.disabled = true;
      searchInputStatus.disabled = true;
      searchInputName.disabled = false;

      searchInputStartDate.classList.add('d-none');
      searchInputEndDate.classList.add('d-none');
      searchInputStatus.classList.add('d-none');
      searchInputName.classList.remove('d-none');

      break;
  }


  });
      


  /////////////////////////////////
  //VIEW TASK//////////////////////
  /////////////////////////////////

  // Récupérer tous les éléments ayant la classe viewTaskBtn
  const viewButtons = document.getElementsByClassName('viewTaskBtn');

  for (const button of viewButtons) {
  button.addEventListener('click', function(event) {
    button.disabled = true;

    setTimeout(function () {
        button.disabled = false;
    }, 1000);
    let tr = event.target.parentNode.parentNode.parentNode.parentNode;
    let task_id = tr.children[1].textContent;

    task_id = parseInt(task_id);
    // Envoi de la requête AJAX
    fetch('/gestion_projet/show_project/{{ $project->id }}/{{ $phase }}/{{ $phase->id }}/tasks/get_task_info/' + task_id)
      .then(response => response.json())
      .then(data => {
        // Récupération des éléments DOM par leur ID
          let name = document.getElementById('view_name');
          let description = document.getElementById('view_description');
          let dateFin = document.getElementById('view_end_date');
          let dateDebut = document.getElementById('view_start_date');
          let status = document.getElementById('view_status');
          let user = document.getElementById('view_user');
          let header_name = document.getElementById('viewTaskModalLabel');

          let confirmed = document.getElementById('confirmed');
          let view_confirmation_date = document.getElementById('view_confirmation_date');
          let view_comment = document.getElementById('view_comment');

          name.value = data.task.name;
          description.value = data.task.description;
          dateFin.value = data.task.end_date;
          dateDebut.value = data.task.start_date;
          view_confirmation_date.value = data.task.confirmed_date;
          view_comment.value = data.task.comment;

          switch (data.task.status)
          {
            case '{{ \App\Enums\TaskStatusEnum::enCours }}':
              status.value = 'En Cours';
              break;
            case '{{ \App\Enums\TaskStatusEnum::enAttente }}':
              status.value = 'En Attente';
              break;
            case '{{ \App\Enums\TaskStatusEnum::nonAboutti }}':
              status.value = 'Non Aboutti';
              break;
            case '{{ \App\Enums\TaskStatusEnum::Terminé }}':
              status.value = 'Terminé';
              break;
            case '{{ \App\Enums\TaskStatusEnum::Expiré }}':
              status.value = 'Expiré';
              break;
          }

          if (data.task.confirmed_date != null)
          {
            confirmed.classList.remove('d-none');
          }
          else
          {
            confirmed.classList.add('d-none');
          }


          header_name.innerText = data.task.name;
          user.value = data.user.first_name + ' ' + data.user.last_name;

          
          

        let myModal_view = new bootstrap.Modal(document.getElementById('viewTaskModal'));
        myModal_view.show();
      })
      .catch(error => {
        console.error(error);
      });
  });
}

  

    /////////////////////////////////////////////////////////
    //Les differnets paginations et tableau//////////////////
    /////////////////////////////////////////////////////////

    const task_option = document.getElementsByClassName('task_option');


      for (let option of task_option)
      {
          option.addEventListener('change', function (event) 
          {
              
            let active_tab = option.value;

              fetch('/gestion_projet/show_project/{{ $project->id }}/{{ $phase }}/{{ $phase->id }}/tasks/fetchTasks/' + active_tab)
              .then(response => response.json())
              .then(data => {
                  // Mettre à jour la pagination ici
                  // Par exemple, réinitialiser la page à 1
                  const newPage = 1;
                  const url = new URL(window.location.href);
                  console.log(window.location.href);
                  url.searchParams.set('page', newPage);
                  window.location.href = url.toString();
              })
              .catch(error => {
                  console.error(error);
              });
          }); 
      }

  function clean_edit() {
      const status = document.getElementById('edit_status');
      const userSelect = document.getElementById('edit_user');

      while (status.firstChild) {
        status.removeChild(status.firstChild);
      }

      while (userSelect.firstChild) {
        userSelect.removeChild(userSelect.firstChild);
      }
    }

  ////////////////////////////////////////////////////////////////////////////////////////////
  //Pour annuler l'envoie plusieurs fois//////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////

  let avoidForm = document.getElementById('addTaskForm'); // Nom du formulaire d'ajout
  

    avoidForm.addEventListener('submit', function () {

    let avoidBtn = document.getElementById('createTaskBtn'); // Nom du bouton d'ajout

    avoidBtn.disabled = true;

    setTimeout(function () {
        avoidBtn.disabled = false;
    }, 3000);
  });

 </script>
@endsection