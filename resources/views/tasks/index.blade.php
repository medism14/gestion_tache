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
                    <div class="col-md-3 d-flex justify-content-md-end justify-content-sm-start mt-md-0 mt-sm-2">
                      
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end justify-content-sm-start mt-md-0 mt-sm-2">

                          
                    </div>
                </div>
              </div>

              <!-- /.card-header -->
              <div class="card-body">
                     <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10">

                          <form action="{{ route('tasks.search') }}" method="POST">
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
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th></th>
      <th>Nom</th>
      <th>Projet</th>
      <th>Statut</th>
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
        $tempsRestantEnCours = $intervalEnCours->format('%a jours, %h heures, %i minutes, %s secondes');
        $tempsRestantEnAttente = $intervalEnAttente->format('%a jours, %h heures, %i minutes, %s secondes');

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
          }
      @endphp


        <tr>
          <td class="{{ $taskStatusClass }}">{{ $i++ }}</td>
          <td class="id d-none {{ $taskStatusClass }}">{{ $item->id }}</td>
          <td class="{{ $taskStatusClass }}">{{ $item->name }}</td>
          <td class="{{ $taskStatusClass }}">{{ $item->phase->project->name }}</td>
          <td class="{{ $taskStatusClass }}">
            @switch ($item->status)
              @case (\App\Enums\TaskStatusEnum::enAttente)
                En Attente (Prévu Dans : {{ $tempsRestantEnAttente }})
                @break
              @case (\App\Enums\TaskStatusEnum::enCours)
                En cours (Temps restant : {{ $tempsRestantEnCours }})
                @break
              @case (\App\Enums\TaskStatusEnum::nonAboutti)
                Non Aboutti
                @break
              @case (\App\Enums\TaskStatusEnum::Terminé)
                Terminé
                @break
              @case (\App\Enums\TaskStatusEnum::Expiré)
                Expiré (Le {{ $endDate }})
                @break
            @endswitch
          </td>
          <td>
            @if ($item->status == \App\Enums\TaskStatusEnum::enCours)
              <div class="d-flex justify-content-between align-items-center">
              <a href="#" class="text-primary viewTaskBtn btn" onclick="return false;" data-bs-placement="top" title="Voir la tâche">
                <i class="fas fa-eye"></i>
              </a>
            
                <button class="text-success confirmTaskBtn btn" onclick="return false;" data-bs-placement="top" title="Confirmer la tâche">
                  <i class="fas fa-check-circle"></i>
                </button>

                <button class="text-danger cancelTaskBtn btn" onclick="return false;" data-bs-placement="top" title="Annuler la tâche">
                  <i class="fas fa-times-circle"></i>
                </button>
            @else 
                <div class="d-flex justify-content-center align-items-center">
                   <a href="#" class="text-primary viewTaskBtn btn" onclick="return false;" data-bs-placement="top" title="Voir la tâche">
                    <i class="fas fa-eye"></i>
                  </a>

            @endif

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
      <th>Projet</th>
      <th>Statut</th>
      <th>Actions</th>
    </tr>
  </tfoot>
</table>
</div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

  @if (isset($search))
    <div class="container d-flex justify-content-center align-items-center mt-4">
    <a href="{{ route('changementSession') }}" class="btn btn-primary">Revenir à la page d'index</a>
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

<!---------------------------------- Modal vue tache -------------------------------->
    <div class="modal" id="viewTaskModal" tabindex="-1" aria-labelledby="viewTaskModalLabel" aria-hidden="true">
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

          <!---------------------------------- Modal Confirmation -------------------------------->
          <div class="modal fade" id="confirmTaskModal" tabindex="-1" aria-labelledby="confirmTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="confirmTaskModalLabel">Confirmer la tâche</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form method="POST" action="{{ route('tasks.confirm') }}" id="confirmTaskForm">
                      @csrf
                        <div class="row mb-4">
                          <div class="col-md-3"></div>
                            <div class="col-md-6">
                              <div class="form-group d-none">  
                                <label for="id">ID</label>
                                <input type="text" class="form-control" id="confirmId" name="id" required>
                              </div>
                              <div class="form-group">
                              <label for="confirm_comment">Commentaire</label><br>
                              <textarea rows="4" cols="50" name="confirm_comment" id="confirm_comment" required></textarea>
                            </div>
                          </div>
                          <div class="col-md-3"></div>
                        </div>

                        <div class="d-flex justify-content-center">
                          <button type="submit" class="btn btn-primary me-2" id="confirmTaskBtn">Transmettre</button>
                          <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">Annuler</button>
                        </div>
                  </form>
                </div>
              </div>
            </div>
          </div> 
              

          <!---------------------------------- Modal Annulation -------------------------------->
          <div class="modal fade" id="cancelTaskModal" tabindex="-1" aria-labelledby="cancelTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="cancelTaskModalLabel">Annuler la tâche</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form method="POST" action="{{ route('tasks.cancel') }}" id="cancelTaskForm">
                      @csrf
                        <div class="row mb-4">
                          <div class="col-md-3"></div>
                            <div class="col-md-6">
                              <div class="form-group d-none">  
                                <label for="id">ID</label>
                                <input type="text" class="form-control" id="cancelId" name="id" required>
                              </div>
                              <div class="form-group">
                              <label for="cancel_comment">Commentaire</label><br>
                              <textarea rows="4" cols="50" name="cancel_comment" id="cancel_comment" required></textarea>
                            </div>
                          </div>
                          <div class="col-md-3"></div>
                        </div>

                        <div class="d-flex justify-content-center">
                          <button type="submit" class="btn btn-primary me-2" id="cancelTaskBtn">Transmettre</button>
                          <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">Annuler</button>
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


        let viewTaskBtns = document.querySelectorAll('.viewTaskBtn');
        viewTaskBtns.forEach(function(btn) {
            new bootstrap.Tooltip(btn);
        });

        let confirmBtns = document.getElementsByClassName('confirmTaskBtn');

        for (let btn of confirmBtns)
        {
          new bootstrap.Tooltip(btn);
        }

        let cancelBtns = document.getElementsByClassName('cancelTaskBtn');

        for (let btn of cancelBtns)
        {
          new bootstrap.Tooltip(btn);
        }

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


   
      


  /////////////////////////////////
  //VIEW TASK//////////////////////
  /////////////////////////////////

  // Récupérer tous les éléments ayant la classe 'fas fa-eye'
  const viewButtons = document.getElementsByClassName('fas fa-eye');

  for (const button of viewButtons) {
  button.addEventListener('click', function(event) {
    let task_id = event.target.parentNode.parentNode.parentNode;

      if (task_id.tagName === 'TR')
      {
        task_id = task_id.children[1].textContent;
      }
      else
      {
        task_id = task_id.parentNode.children[1].textContent;
      }

    // Envoi de la requête AJAX
    fetch('/gestion_tache/get_task_info/' + task_id)
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
          view_confirmation_date = data.task.confirmation_date;
          view_comment = data.task.comment;

          switch (data.task.status)
          {
            case '{{ \App\Enums\TaskStatusEnum::enCours }}':
              status.value = 'En cours';
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
  //CONFIRM TASK///////////////////
  /////////////////////////////////

  const confirmTaskBtns = document.getElementsByClassName('confirmTaskBtn');

  for (const btn of confirmTaskBtns)
  {
    btn.addEventListener('click', function (event) {

      let confirm_comment = document.getElementById('confirm_comment');
      confirm_comment.value = null;

      let task_id = event.target.parentNode.parentNode.parentNode;
      let text = event.target.parentNode.parentNode.parentNode;

      if (task_id.tagName === 'TR')
      {
        task_id = task_id.children[1].textContent;
        text = text.children[2].textContent
      }
      else
      {
        task_id = task_id.parentNode.children[1].textContent;
        text = text.parentNode.children[2].textContent;
      }

      let header_name = document.getElementById('confirmTaskModalLabel');

      header_name.innerHTML = 'Confirmer la tâche "' + text + '"';

      let confirmId = document.getElementById('confirmId');

      confirmId.value = task_id;

      let myModal_Confirm = new bootstrap.Modal(document.getElementById('confirmTaskModal'));
      myModal_Confirm.show();

    });
  }


  /////////////////////////////////
  //CANCEL TASK///////////////////
  /////////////////////////////////

  const cancelTaskBtns = document.getElementsByClassName('cancelTaskBtn');

  for (const btn of cancelTaskBtns)
  {
    btn.addEventListener('click', function (event) {

      let cancel_comment = document.getElementById('cancel_comment');
      cancel_comment.value = null;

      let task_id = event.target.parentNode.parentNode.parentNode;
      let text = event.target.parentNode.parentNode.parentNode;

      if (task_id.tagName === 'TR')
      {
        task_id = task_id.children[1].textContent;
        text = text.children[2].textContent
      }
      else
      {
        task_id = task_id.parentNode.children[1].textContent;
        text = text.parentNode.children[2].textContent;
      }

      let header_name = document.getElementById('cancelTaskModalLabel');

      header_name.innerHTML = 'Annuler la tâche "' + text + '"';

      let cancelId = document.getElementById('cancelId');

      cancelId.value = task_id;

      let myModal_Cancel = new bootstrap.Modal(document.getElementById('cancelTaskModal'));
      myModal_Cancel.show();

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

              fetch('/gestion_tache/fetchTasks/' + active_tab)
              .then(response => response.json())
              .then(data => {
                  // Mettre à jour la pagination ici
                  // Par exemple, réinitialiser la page à 1
                  const newPage = 1;
                  const url = new URL(window.location.href);
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

 </script>
@endsection