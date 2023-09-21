@extends('layouts.app')

@section('content')

  <style>
    @media (min-width: 768px)
    {
      .custom-width-md
      {
        width: 50%;
      }
    }
  </style>

  <div class="card">
    <div class="card-header">
      <div class="row d-flex align-items-center">
          <div class="col-md-3">
            <h3 class="card-title"><u><b>{{ $project->name }}</u></b></h3>
         </div>  
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        @if (session('typeProject') == 'project')
          <a href="{{ route('projects.index') }}" class="btn btn-secondary float-left"><i class="fas fa-arrow-left"></i> Retour</a>
        @else
          <a href="{{ route('projects.archive') }}" class="btn btn-secondary float-left"><i class="fas fa-arrow-left"></i> Retour</a>
        @endif

        <div class="d-flex justify-content-center">
            <div class="form-group mr-3">
              <label for="prospection">Prospection</label>
              <input type="radio" name="choixPhase" class="choixPhase" value="prospection" style="cursor:pointer;" checked>
            </div>

            <div class="form-group mr-3">
              <label for="execution">Execution</label>
              <input type="radio" name="choixPhase" class="choixPhase" value="execution" style="cursor:pointer;">
            </div>

            <div class="form-group">
              <label for="recouvrement">Recouvrement</label>
              <input type="radio" name="choixPhase" class="choixPhase" value="recouvrement" style="cursor:pointer;">
            </div>
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
              <th>Phase</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @php
            $i = 1;
          @endphp

            <tr>
              <td class="d-none">{{ $project->id }}</td>
              <td class="align-middle" id="prospection">
                  @foreach ($phases as $item)
                  @if ($item->name == 'Prospection')
                      @if ($item->status == 'en_cours')
                        <div class="row d-flex justify-content-center mb-4">
                            <button class="btn btn-success custom-width-md" style="cursor:default;">En Cours</button>
                        </div>

                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksProspectionenAttente }} tâches en attente</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksProspectionenCours }} tâches en cours</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksProspectionnonAboutti }} tâches non aboutti</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksProspectionTerminé }} tâches terminé</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-4 rounded-lg">{{ $countTasksProspectionExpiré }} tâches expiré</button></div>

                        <div class="row">
                          @if ($project->status == \App\Enums\ProjectStatusEnum::enCours)
                          <div class="col-md-6 d-flex justify-content-md-start">
                            <a href="{{ route('projects.tasks.index', ['id' => $item->project_id ,'phase' => $item->name ,'phase_id' => $item->id]) }}" class="btn btn-info" style="cursor:pointer;"><i class="fas fa-arrow-circle-right"></i> Voir les tâches</a>
                          </div>                    
                          
                            <div class="col-md-6 d-flex justify-content-md-end mt-md-0 mt-3">
                              <a href="{{ route('projects.phase.confirm', [
                                'id' => $item->project->id,
                                'phase_id' => $item->id
                              ]) }}" class="btn btn-warning confirmBtn" style="cursor:pointer;"><i class="fas fa-check-circle text-white"></i>
                                Confirmer la phase</a>
                            </div>
                          @else

                            <div class="d-flex justify-content-center mb-4 mb-md-0">
                              <a href="{{ route('projects.tasks.index', ['id' => $item->project_id ,'phase' => $item->name ,'phase_id' => $item->id]) }}" class="btn btn-info" style="cursor:pointer;"><i class="fas fa-arrow-circle-right"></i> Voir les tâches</a>
                            </div>

                          @endif
                        </div>
                      @endif

                      @if ($item->status == 'en_attente')
                      <div class="row d-flex justify-content-center mb-4">
                        <button class="btn btn-danger custom-width-md" style="cursor:default;">Phase en Attente</button>
                      </div>
                      @endif

                      @if ($item->status == 'termine')
                      <div class="row d-flex justify-content-center mb-4">
                        <button class="btn btn-secondary custom-width-md" style="cursor:default;">Phase Terminé</button>
                      </div>

                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksProspectionenAttente }} tâches en attente</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksProspectionenCours }} tâches en cours</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksProspectionnonAboutti }} tâches non aboutti</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksProspectionTerminé }} tâches terminé</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-4 rounded-lg">{{ $countTasksProspectionExpiré }} tâches expiré</button></div>

                      <div class="row">
                        <a href="{{ route('projects.tasks.index', ['id' => $item->project_id ,'phase' => $item->name ,'phase_id' => $item->id]) }}" class="btn btn-info" style="cursor:pointer;"><i class="fas fa-arrow-circle-right"></i> Voir les tâches</a>
                      </div>
                      @endif
                  @endif
                  @endforeach  
                </td>

                <td class="align-middle d-none" id="execution">
                 @foreach ($phases as $item) 
                  @if ($item->name == 'Execution')
                      @if ($item->status == 'en_cours')
                        <div class="row d-flex justify-content-center mb-4">
                            <button class="btn btn-success custom-width-md" style="cursor:default;">En Cours</button>
                        </div>

                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksExecutionenAttente }} tâches en attente</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksExecutionenCours }} tâches en cours</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksExecutionnonAboutti }} tâches non aboutti</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksExecutionTerminé }} tâches terminé</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-4 rounded-lg">{{ $countTasksExecutionExpiré }} tâches expiré</button></div>

                        <div class="row">
                          @if ($project->status == \App\Enums\ProjectStatusEnum::enCours)
                          <div class="col-md-6 d-flex justify-content-md-start">
                            <a href="{{ route('projects.tasks.index', ['id' => $item->project_id ,'phase' => $item->name ,'phase_id' => $item->id]) }}" class="btn btn-info" style="cursor:pointer;"><i class="fas fa-arrow-circle-right"></i> Voir les tâches</a>
                          </div>                    
                          
                            <div class="col-md-6 d-flex justify-content-md-end mt-md-0 mt-3">
                              <a href="{{ route('projects.phase.confirm', [
                                'id' => $item->project->id,
                                'phase_id' => $item->id
                              ]) }}" class="btn btn-warning confirmBtn" style="cursor:pointer;"><i class="fas fa-check-circle text-white"></i>
                                Confirmer la phase</a>
                            </div>
                          @else

                            <div class="d-flex justify-content-center mb-4 mb-md-0">
                              <a href="{{ route('projects.tasks.index', ['id' => $item->project_id ,'phase' => $item->name ,'phase_id' => $item->id]) }}" class="btn btn-info" style="cursor:pointer;"><i class="fas fa-arrow-circle-right"></i> Voir les tâches</a>
                            </div>

                          @endif
                        </div>
                      @endif

                      @if ($item->status == 'en_attente')
                      <div class="row d-flex justify-content-center mb-4">
                        <button class="btn btn-danger custom-width-md" style="cursor:default;">Phase en Attente</button>
                      </div>
                      @endif

                      @if ($item->status == 'termine')
                      <div class="row d-flex justify-content-center mb-4">
                        <button class="btn btn-secondary custom-width-md" style="cursor:default;">Phase Terminé</button>
                      </div>

                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksExecutionenAttente }} tâches en attente</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksExecutionenCours }} tâches en cours</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksExecutionnonAboutti }} tâches non aboutti</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksExecutionTerminé }} tâches terminé</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-4">{{ $countTasksExecutionExpiré }} tâches expiré</button></div>

                      <div class="row">
                            <a href="{{ route('projects.tasks.index', ['id' => $item->project_id ,'phase' => $item->name ,'phase_id' => $item->id]) }}" class="btn btn-info" style="cursor:pointer;"><i class="fas fa-arrow-circle-right"></i> Voir les tâches</a>
                      </div>
                      @endif
                  @endif
                  @endforeach  
                </td>

                <td class="align-middle d-none" id="recouvrement">
                 @foreach ($phases as $item) 
                  @if ($item->name == 'Recouvrement')
                      @if ($item->status == 'en_cours')
                        <div class="row d-flex justify-content-center mb-4">
                            <button class="btn btn-success custom-width-md" style="cursor:default;">En Cours</button>
                        </div>

                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksRecouvrementenAttente }} tâches en attente</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksRecouvrementenCours }} tâches en cours</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksRecouvrementnonAboutti }} tâches non aboutti</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksRecouvrementTerminé }} tâches terminé</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-4">{{ $countTasksRecouvrementExpiré }} tâches expiré</button></div>

                        <div class="row">
                          @if ($project->status == \App\Enums\ProjectStatusEnum::enCours)
                          <div class="col-md-6 d-flex justify-content-md-start">
                            <a href="{{ route('projects.tasks.index', ['id' => $item->project_id ,'phase' => $item->name ,'phase_id' => $item->id]) }}" class="btn btn-info" style="cursor:pointer;"><i class="fas fa-arrow-circle-right"></i> Voir les tâches</a>
                          </div>                    
                          
                            <div class="col-md-6 d-flex justify-content-md-end mt-md-0 mt-3">
                              <a href="{{ route('projects.phase.confirm', [
                                'id' => $item->project->id,
                                'phase_id' => $item->id
                              ]) }}" class="btn btn-warning confirmBtn" style="cursor:pointer;"><i class="fas fa-check-circle text-white"></i>
                                Confirmer la phase</a>
                            </div>
                          @else

                            <div class="d-flex justify-content-center mb-4 mb-md-0">
                              <a href="{{ route('projects.tasks.index', ['id' => $item->project_id ,'phase' => $item->name ,'phase_id' => $item->id]) }}" class="btn btn-info" style="cursor:pointer;"><i class="fas fa-arrow-circle-right"></i> Voir les tâches</a>
                            </div>

                          @endif
                        </div>
                      @endif

                      @if ($item->status == 'en_attente')
                      <div class="row d-flex justify-content-center mb-4">
                        <button class="btn btn-danger custom-width-md" style="cursor:default;">Phase en Attente</button>
                      </div>
                      @endif

                      @if ($item->status == 'termine')
                      <div class="row d-flex justify-content-center mb-4">
                        <button class="btn btn-secondary custom-width-md" style="cursor:default;">Phase Terminé</button>
                      </div>

                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksRecouvrementenAttente }} tâches en attente</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksRecouvrementenCours }} tâches en cours</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksRecouvrementnonAboutti }} tâches non aboutti</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-2 rounded-lg">{{ $countTasksRecouvrementTerminé }} tâches terminé</button></div>
                      <div class="row"><button class="btn btn-outline-dark mb-4">{{ $countTasksRecouvrementExpiré }} tâches expiré</button></div>

                      <div class="row">
                            <a href="{{ route('projects.tasks.index', ['id' => $item->project_id ,'phase' => $item->name ,'phase_id' => $item->id]) }}" class="btn btn-info" style="cursor:pointer;"><i class="fas fa-arrow-circle-right"></i> Voir les tâches</a>
                      </div>
                      @endif
                  @endif
                  @endforeach  
              </td>
              <td class="align-middle" style="width: 100px">
                <div class="{{ $project->status == \App\Enums\ProjectStatusEnum::enCours ? 'd-flex justify-content-center' : 'd-flex justify-content-center' }}">
                  <button class="text-primary viewProjectBtn btn {{ $project->status == \App\Enums\ProjectStatusEnum::enCours ? 'mr-5' : '' }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Afficher en détail" style="border: none; background: none; padding: 0;">
                    <i class="fas fa-eye"></i>
                  </button>

                  @if ($project->status == \App\Enums\ProjectStatusEnum::enCours)
                  <a href="{{ route('projects.cancel', ['id' => $project->id]) }}" class="text-danger unsuccesfulProjectBtn" data-bs-toggle="tooltip" data-bs-placement="top" title="Annuler le projet" onclick="return confirm('Êtes-vous sûr de vouloir annuler ce projet ? Cette action est irréversible.');" style="border: none; background: none; padding: 0;">
                    <i class="fas fa-ban"></i>
                  </a>
                  @endif
                </div>
              </td>
            </tr>
        </tbody>
        <tfoot>
          <tr>
              <th>Phase</th>
            <th>Actions</th>
          </tr>
        </tfoot>
      </table>

          <!---------------------------------- Modal vue utilisateur -------------------------------->
  <div class="modal" id="viewProjectModal" tabindex="-1" aria-labelledby="viewProjectModalLabel" aria-hadden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewProjectModalLabel"></h5>
          <button class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">  
                <label for="name">Nom</label>
                <input readonly type="text" class="form-control" id="name" name="name" required>
              </div>

              <div class="form-group">
                <label for="description">Description</label>
                <textarea disabled name="description" id="description" class="form-control"></textarea>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group">
                <label for="start_date">Date de début</label>
                <input readonly type="tel" class="form-control" id="start_date" name="start_date" required>
              </div>

              <div class="form-group">
                <label for="status">Statut</label>
                <input readonly class="form-control" id="status" name="status" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4 d-flex justify-content-center">
              <div class="form-group">
                <label for="projectMembers">Membres du groupe</label>
                <div id="projectMembers">
                  <!-- Informations des utilisateurs ici -->
                    <span>qfsdlkkljdfqs</span>
                </div>
              </div>
            </div>
            <div class="col-md-4"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
    </div>
  </div>






@endsection
@section('scripts')
  <script>

    ////////////////////////////////////////////////////////////////////
    //////////////////////////////////
    //Manipulation de tooltip et modal
    //////////////////////////////////
    ////////////////////////////////////////////////////////////////////
    
    ////////////////////////////////////////////////////
    //Pour afficher les tooltip du viewProject en détail
    ////////////////////////////////////////////////////

    const viewsProjectBtn = document.getElementsByClassName('viewProjectBtn');

    for (let viewBtn of viewsProjectBtn)
    {
      new bootstrap.Tooltip(viewBtn);
    }

    //////////////////////////////////////////////////
    //Pour afficher les tooltip du unsucessfulyProject
    //////////////////////////////////////////////////

    const unsuccesfulProjectBtn = document.getElementsByClassName('unsuccesfulProjectBtn');

    for (let btn of unsuccesfulProjectBtn)
    {
      new bootstrap.Tooltip(btn);
    }


    //////////////////////////////////////////////////
    /////////////////////////
    //Vue complet d'un projet
    /////////////////////////
    //////////////////////////////////////////////////

    for (let btn of viewsProjectBtn)
    {
      btn.addEventListener('click', function (event) {
        btn.disabled = true;

    setTimeout(function () {
        btn.disabled = false;
    }, 1000);
        let id = event.target.parentNode.parentNode.parentNode.parentNode.children[0].textContent;

        id = parseInt(id);

        fetch('/gestion_projet/get_project_info/' + id)
        .then(response => response.json())
        .then(data => {
          cleanModalView();
          const name = document.getElementById('name');
          const description = document.getElementById('description')
          const start_date = document.getElementById('start_date');
          const status = document.getElementById('status');
          const projectMembers = document.getElementById('projectMembers');
          const header_name = document.getElementById('viewProjectModalLabel');

          name.value = data.project.name;
          description.value = data.project.description;
          start_date.value = data.project.start_date;
          header_name.innerText = data.project.name;

          switch (data.project.status)
          {
            case 'en_cours':
              status.value = 'En Cours';
              break;
            case 'non_aboutti':
              status.value = 'Non Aboutti';
              break;
            case 'termine':
              status.value = 'Terminé';
              break;

            default:
          }

          for (user of data.usersOnGroup)
          {
            let span = document.createElement('span');
            span.innerHTML = '- ' + user.first_name + ' ' + user.last_name + '<br>';
            projectMembers.appendChild(span);
          }

          let myModal = new bootstrap.Modal(document.getElementById('viewProjectModal'));

          myModal.show();
        });

      })
    }

    function cleanModalView () {
      const name = document.getElementById('name');
      const description = document.getElementById('description')
      const start_date = document.getElementById('start_date');
      const status = document.getElementById('status');
      const projectMembers = document.getElementById('projectMembers');

      name.value = '';
      description.value = '';
      start_date.value = '';
      status.value = '';

      while (projectMembers.firstChild) {
        projectMembers.removeChild(projectMembers.firstChild);
      }
    }

    //////////////////////////////////////////////////
    /////////////////////////
    //Manipulation Bonnus
    /////////////////////////
    //////////////////////////////////////////////////

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

    //////////////////////////////////////////
    //Changement de phase/////////////////////
    //////////////////////////////////////////

    choixPhase = document.getElementsByClassName('choixPhase');

    for (phase of choixPhase)
    {
      phase.addEventListener('change', function (event) {
        let item = event.target;

        let prospectionPhase = document.getElementById('prospection');

        let executionPhase = document.getElementById('execution');

        let recouvrementPhase = document.getElementById('recouvrement');

        switch (item.value)
        {
          case 'prospection':
            prospectionPhase.classList.remove('d-none');
            executionPhase.classList.add('d-none');
            recouvrementPhase.classList.add('d-none');
            break;

          case 'execution':
            prospectionPhase.classList.add('d-none');
            executionPhase.classList.remove('d-none');
            recouvrementPhase.classList.add('d-none');
            break;
          case 'recouvrement':
            prospectionPhase.classList.add('d-none');
            executionPhase.classList.add('d-none');
            recouvrementPhase.classList.remove('d-none');
            break;
        }

      })
    }

  </script> 
@endsection
