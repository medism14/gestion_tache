<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <!-- Mettez ici les liens vers les feuilles de style -->
  <!-- Boostrap 5 Css-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
  <!-- fontawesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            position: relative;
            top: 50%;
            transform: translateY(-50%);
        }
        .login-heading {
            font-size: 28px;
            font-weight: 500;
            margin-bottom: 30px;
        }
        .form-control:focus {
            box-shadow: none;
        }
        .login-btn {
            background-color: #007bff;
            color: #fff;
        }
        .login-btn:hover {
            background-color: #0056b3;
        }
        .mt-3 {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="d-flex justify-content-center mt-3">
        <img src="{{ asset('images/n-tech-2.jpg') }}" height="100">
    </div>
    @yield('content')

    <!-- Mettez ici les liens vers les scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <!-- Boostrap 5 JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    
</body>
</html>
