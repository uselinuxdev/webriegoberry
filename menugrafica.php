<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
            <link href="css/jquery-ui.css" rel="stylesheet" type="text/css"/> 
            <script src="java/jquery.js"></script>
            <script src="java/jquery-ui.js"></script>
             <script>
                $(function() {
                    $( "#menuacordeon" ).accordion();
                });
            </script>
    </head>
    <body>
        <?php
            //echo $_SESSION['pag'];
        ?>
        <div id="menuacordeon">
            <h4>
                <h4>
                    <?php
                    if ($_SESSION['pag'] == 'riegohoras.php') {
                        echo "<pos>";
                    }else{
                        echo "<npos>";
                    }         
                    ?>
                    <p onClick="location.href='riegohoras.php'" onMouseover=""  style=" cursor: pointer;">VALORES INSTANTANEOS</p>
                    </pos> 
                    <npos>
                    <br>
                    <?php
                    if ($_SESSION['pag'] == 'riegodia.php') {
                        echo "<pos>";
                    }else{
                        echo "<npos>";
                    }         
                    ?>
                    <p onClick="location.href='riegodia.php'" onMouseover=""  style=" cursor: pointer;">VALORES POR HORAS</p>
                    </pos> 
                    <npos>
                    <br>
                    <?php
                    if ($_SESSION['pag'] == 'riegobinarios.php') {
                        echo "<pos>";
                    }else{
                        echo "<npos>";
                    }         
                    ?>
                    <p onClick="location.href='riegobinarios.php'" onMouseover=""  style=" cursor: pointer;">VALORES DIGITALES</p>
                    </pos> 
                    <npos>
                    <br>
                </h4>
            </h4>
        </div>
        <div id="divriegotec">
            <img src="imagenes/riego-tec.jpg" alt="Logpie"/>
        </div>
    </body>
</html>

