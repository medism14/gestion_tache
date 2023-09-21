@extends('layouts.app')

@section('content')
  <!-- Content Header (Page header) -->
    <!-- Chart JS -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Tableau de bord</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->

          @if (auth()->user()->role == '0')
            <div class="row">
              <div class="col-4">
                <!-- small box -->
                <div class="small-box bg-success">
                  <div class="inner">
                    <h3>{{ $count_projectInProgess }}</h3>

                    <p>Projets en cours</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-calendar-day fs-1"></i>
                  </div>
                  <a href="{{ route('projects.index') }}" class="small-box-footer">Plus d'informations <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <div class="col-4">
                <div class="">
                    <!-- small box -->
                    <div class="small-box bg-info">
                      <div class="inner">
                        <h3>{{ $count_users }}</h3>

                        <p>Nombre d'utilisateurs</p>
                      </div>
                      <div class="icon">
                        <i class="fas fa-user-plus fs-1"></i>
                      </div>
                      <a href="#" class="small-box-footer">Plus d'informations <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                  </div>
                </div>
              <div class="col-4">
                <!-- small box -->
                <div class="small-box bg-danger">
                  <div class="inner">
                    <h3>{{ $count_inProgessTasks_admin }}</h3>

                    <p>Taches d'aujourd'hui</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-chart-bar fs-1"></i>
                  </div>
                  <a href="{{ route('tasks.today') }}" class="small-box-footer">Plus d'informations <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
            </div>

          @endif
            <div class="row">
              <div class="col-4">
                <!-- small box -->
                <div class="small-box bg-secondary">
                  <div class="inner">
                    <h3>{{ $count_todayTasks }}</h3>

                    <p>Tâche disponible aujourd'hui pour vous</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-tasks fs-1"></i>
                  </div>
                  <a href="{{ route('tasks.searchDashboard', ['type' => 'todayTasks']) }}" class="small-box-footer">Plus d'informations <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-4">
                <!-- small box -->
                <div class="small-box bg-light">
                  <div class="inner">
                    <h3>{{ $count_inProgessTasks }}</h3>

                    <p>Tâches en cours pour vous</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-calendar-day fs-1"></i>
                  </div>
                  <a href="{{ route('tasks.searchDashboard', ['type' => 'inProgessTasks']) }}" class="small-box-footer">Plus d'informations <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
              <div class="col-4">
                <!-- small box -->
                <div class="small-box bg-warning">
                  <div class="inner">
                    <h3>{{ $count_onWaitingTasks }}</h3>

                    <p>Tache en attente pour vous</p>
                  </div>
                  <div class="icon">
                    </i><i class="fas fa-chart-bar fs-1"></i>
                  </div>
                  <a href="{{ route('tasks.searchDashboard', ['type' => 'onWaitingTasks']) }}" class="small-box-footer">Plus d'informations <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
              <!-- ./col -->
            </div>
          <!-- ./col -->
          
          <!-- Élément canvas pour le graphique -->
          <canvas id="myChart"></canvas>

      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@section('scripts')
  
    <script>
        // Exemple de données (à remplacer par vos données réelles)
        var days = ['7 Jours Avant', '6 Jours Avant', '5 Jours Avant', '4 Jours Avant', '3 Jours Avant', 'Avant-Hier', 'Hier'];

        var tasksCreated = [{{ $pourcentage_tasks_seven_day_before }}, {{ $pourcentage_tasks_six_day_before }}, {{ $pourcentage_tasks_five_day_before }}, {{ $pourcentage_tasks_four_day_before }}, {{ $pourcentage_tasks_three_day_before }}, {{ $pourcentage_tasks_two_day_before }}, {{ $pourcentage_tasks_yesterday }}];

        // Obtenez l'élément canvas
        var ctx = document.getElementById('myChart').getContext('2d');

        // Initialisez le graphique
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: days,
                datasets: [{
                    label: 'Pourcentages de tâche (accomplis + non aboutti / expiré)',
                    data: tasksCreated,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 3
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                    }
                }
            }
        });
    </script>

@endsection
