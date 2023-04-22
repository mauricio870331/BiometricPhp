<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Verificacion de usuarios</title>
        <link rel="stylesheet" href="../public/css/estilos.css"/>
    </head>
    <body>
        <h2>Verificar Usuarios</h2>
        <div id="content_">
            <div>
                <div>
                    <div style=" float: right">
                        <div class="date">
                            <span class="colorGray">
                                <span class="colorGray" id="weekDay" class="weekDay" ></span>,
                                <span class="colorGray" id="day" class="day" ></span> de
                                <span class="colorGray" id="month" class="mont" ></span> del
                                <span class="colorGray" id="year" class="year" ></span>
                            </span>
                        </div>
                        <div class="clock">                                            
                            <span class="colorGray"  class="hours" >Hora: </span>
                            <span class="colorGray" id="hours" class="hours" ></span>:
                            <span class="colorGray" id="minutes" class="minutes" ></span>:
                            <span class="colorGray" id="seconds" class="seconds" ></span>
                        </div>
                    </div>           
                </div>
                <div>
                    <div  style="margin:0px auto;width: 18% !important;">
                        <div style="border: solid 1px; border-radius: 10px;">
                            <div>
                                <div>
                                    <img class="imgFinger" src="../public/images/finger.png" alt="Card image cap">
                                    <h5 class="u_nombre" >--</h5>
                                    <div style="border: solid 1px; border-radius: 10px;" class="location text-sm-center u_identificacion">--</div>
                                </div>
                            </div>
                            <div style="border: solid 1px;background-color: white; border-bottom-left-radius: 10px;border-bottom-right-radius: 10px">
                                <strong class="txtFinger">
                                    ----
                                </strong>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <script src="../public/js/jquery-1.7.2.min.js"></script>
        <script src="../public/js/SweetAlert2.js"></script>
        <script src="../public/js/funciones.js"></script>
        <script src="../public/js/reloj.js"></script>
    </body>
</html>