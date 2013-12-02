<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
/*
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Plume CMS, a website management application.
# Copyright (C) 2001-2006 Loic d'Anterroches and contributors.
#
# Credits: Olivier Meunier.
#
# Plume CMS is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# Plume CMS is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
#
# ***** END LICENSE BLOCK ***** */

class FullCalendar
{

	public static $locale = array(
			'en'=> array(
					'monthNames'=> array('January','February','March','April','May','June',
							'July','August','September','October','November','December'),
					'monthNamesShort'=> array('Jan.','Feb.','Mar.','Apr.','May','June',
							'July','Aug.','Sep.','Oct.','Nov.','Dec.'),
					'dayNames' => array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
					'dayNamesShort' => array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'),
					'txt_noEvents' => 'No events for this',
					'txt_SpecificEvents_prev' => 'events',
					'txt_SpecificEvents_after' => 'events',

					'txt_NextEvents' => 'Next events',
					'txt_GoToEventUrl' => "go to event",
					'buttonText'=> array(
							'prev'=>'previous', 
							'next'=> 'next', 
							'prevYear'=>'&nbsp;&lt;&lt;&nbsp;', 
							'nextYear'=>'&nbsp;&gt;&gt;&nbsp;', 
							'today'=>'today', 
							'month'=>'month', 
							'week'=>'week',
							'day'=>'day'),
	
			),
			'fr' => array (
					'monthNames' => array('Janvier','Février','Mars','Avril','Mai','Juin',
							'Juillet','Août','Septembre','Octobre','Novembre','Décembre'),
					'monthNamesShort' => array('Jan.','Fév.','Mars','Avr.','Mai','Juin',
							'Juil.','Août','Sep.','Oct.','Nov.','Déc.'),
					'dayNames' => array('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'),
					'dayNamesShort' => array('Dim.','Lun.','Mar.','Mer.','Jeu.','Ven.','Sam.'),
					'txt_noEvents' => "Pas d'évènement pour cette période",
					'txt_SpecificEvents_prev' => 'évènements',
					'txt_SpecificEvents_after' => 'évènements',
					'txt_NextEvents' => 'Evènements à venir',
					'txt_GoToEventUrl' => "Voir l'évènement",
					'buttonText'=> array(
							'prev'=>'précédent',
							'next'=> 'suivant',
							'prevYear'=>'&nbsp;&lt;&lt;&nbsp;',
							'nextYear'=>'&nbsp;&gt;&gt;&nbsp;',
							'today'=>'Aujourd\'hui',
							'month'=>'mois',
							'week'=>'semaine',
							'day'=>'jour'),					
			),
			'es' => array(
					'monthNames' => array( "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
							"Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ),
					'monthNamesShort'=> array('Jan.','Feb.','Mar.','Apr.','May','June',
							'July','Aug.','Sep.','Oct.','Nov.','Dec.'),
					'dayNames' => array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
					'dayNames' => array('Domingo','Lunes','Martes','Miércoles', 'Jueves','Viernes','Sabado'),
					'dayNamesShort' => array('Dom','Lun','Mar','Mie', 'Jue','Vie','Sab'),
					'txt_noEvents' => 'No hay eventos para este periodo',
					'txt_SpecificEvents_prev' => 'eventos',
					'txt_SpecificEvents_after' => 'eventos',
					'txt_next' => 'siguiente',
					'txt_prev' => 'anterior',
					'txt_NextEvents' => 'Próximos eventos',
					'txt_GoToEventUrl' => 'Ir al evento',
					'buttonText'=> array(
							'prev'=>'siguiente',
							'next'=> 'anterior',
							'prevYear'=>'&nbsp;&lt;&lt;&nbsp;',
							'nextYear'=>'&nbsp;&gt;&gt;&nbsp;',
							'today'=>'today',
							'month'=>'month',
							'week'=>'week',
							'day'=>'day'),
			),
	);
	
	
    public static function onEventsShow($name, $p)
    { 
    	$p=$p[0];
    	$languepreferee = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);

		$lang = $languepreferee[0];
		//if (!isset(EventCalendar::$locale[$lang])) $lang='en';
		
		// <div class="fullCalendar" data-fullcalendar-href="/home/aslcpsa/agora/module_agenda/agenda_service.php" data-fullcalendar-id-agenda="2" data-fullcalendar-id-espace="1" data_fullcalendar-width="400" data_fullcalendar-width="350" id="fullCalendar-1-2" style="width:70%;">Agenda 9 places</div>
    	$resp = '';
		// Core CSS File. The CSS code needed to make eventCalendar works
		$resp .= '<link rel="stylesheet" href="manager/tools/fullCalendar/fullCalendar/fullCalendar.css">';
		// script ref
		$resp .='<script src="manager/tools/fullCalendar/fullCalendar/fullCalendar.js" type="text/javascript"></script>';
		$resp .= '<script src="manager/js/ui/jquery-ui.custom.min.js" type="text/javascript"></script>';
		$resp .= '<link rel="stylesheet" type="text/css" href="manager/js/ui/css/cupertino/jquery-ui-1.9.2.custom.min.css" media="screen" />';
		$resp .= '<div id="eventCalendarList"></div>';
        $resp .= '<script type="text/javascript">
				    $(document).ready(function() {
				    
				        var ref; 
			        	$(".fullcalendar").each(function(index) {
			        		// effacer ce qui est à l intérieur de la balise
			        		$(this).html("");
			        		var dataCalendar = new Array();
			        		// récupération des paramètres
			        		var attribs = $(this).attr("data-fullcalendar-params").split("&");
			        		for(var i =0; i<attribs.length; i++) {
				        		if (attribs[i] != "") {	
				        			param = attribs[i].split("=");			        			
				        			if (param.length =2) dataCalendar[param[0]]=param[1];
			        			}
			        		}
			        		// format des paramètres
			        		//var dataCalendar = {"id_espace":id_espace, "id_agenda":id_agenda};
	        				$(this).fullCalendar({
								editable: false,
								theme: true,
								monthNames : '.json_encode(FullCalendar::$locale[$lang]['monthNames']).',
								monthNamesShort: '.json_encode(FullCalendar::$locale[$lang]['monthNamesShort']).',
								dayNames: '.json_encode(FullCalendar::$locale[$lang]['dayNames']).',
								dayNamesShort: '.json_encode(FullCalendar::$locale[$lang]['dayNamesShort']).',
								buttonText: '.json_encode(FullCalendar::$locale[$lang]['buttonText']).',
								/*events: $(this).attr("data-fullcalendar-href"),*/
								eventSources: [
        							// your event source
        							{
	            						url: $(this).attr("data-fullcalendar-href"),
	            						type: "GET",
	           							data: dataCalendar,
	           							error: function() {
	                						alert("there was an error while fetching events!");
							            },
							            //color: "yellow",   // a non-ajax option
							            //textColor: "black" // a non-ajax option
        							}

        							// any other sources...

    							],
    							/*
								eventDrop: function(event, delta) {
									alert(event.title + " was moved " + delta + " days\n" +
									"(should probably update your database)");
								},
								*/
								loading: function(bool) {
									if (bool) $("#loading").show();
									else $("#loading").hide();
								}
							}); 
						
						});
				        
				        		
				    });
        	</script>';

        if ($p['return'])
        	return $resp;
        else
        	echo $resp;
    }
}

Hook::register('onFullCalendarShow', 'FullCalendar', 'onEventsShow');

?>