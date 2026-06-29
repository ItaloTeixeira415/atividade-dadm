<?php   
$id = $_GET['id'];
$nome = $_GET['nome'];
?> 
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Microsserviço de Entrega | AdminLTE 3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="../css/all.min.css">
    <!-- Bootstrap (necessário para o AdminLTE 3) -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- AdminLTE 3 -->
    <link rel="stylesheet" href="../css/adminlte.min.css">

    <style>
        /* Ajuste para a API ficar configurável facilmente */
        .badge-pagamento { font-size: .85rem; }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <span class="navbar-text ml-auto mr-3 text-muted">
            <i class="fas fa-network-wired"></i> Microsserviço: <strong>Entrega</strong>
        </span>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="#" class="brand-link">
            <i class="fas fa-truck-fast ml-2 mr-2"></i>
            <span class="brand-text font-weight-light">PUC - Entregas</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="nav-icon fas fa-box"></i>
                            <p>Entregas</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Gestão de Entregas</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <h5>
<?php   
echo $id . " - " . $nome;
?> 
</h5>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> Lista de Entregas</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Endereço</th>
                                <th>Entregador</th>
                                <th>Pagamento</th>
                                <th>Ações</th>
                            </tr>
                            </thead>
                            <tbody id="tabelaEntregas">
                                <tr><td colspan="5" class="text-center">Carregando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <strong>PUC Minas</strong> - Desenvolvimento de Aplicações Distribuídas e Móveis &copy; 2026
    </footer>
</div>

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script src="js/app.js"></script>
</body>
</html>