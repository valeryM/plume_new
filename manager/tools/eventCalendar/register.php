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

class EventCalendar
{
	
	public static $locale = array(
			'en'=> array(
					'monthNames'=> array('January','February','March','April','May','June',
						'July','August','September','October','November','December'),
					'dayNames' => array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
					'dayNamesShort' => array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'),
					'dateFormat' => '$monthNames %dS',
					'txt_noEvents' => 'No events for this',
					'txt_SpecificEvents_prev' => 'Events for ',
					'txt_SpecificEvents_prevDate' => 'Events for ',
					'txt_SpecificEvents_after' => '',
					'txt_next' => 'next',
					'txt_prev' => 'previous',
					'txt_NextEvents' => 'Next events',
					'txt_GoToEventUrl' => "go to event",
						
				),
			'fr' => array (
					'monthNames' => array('Janvier','Février','Mars','Avril','Mai','Juin',
							'Juillet','Août','Septembre','Octobre','Novembre','Décembre'),
					'dayNames' => array('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'),
					'dayNamesShort' => array('Dim.','Lun.','Mar.','Mer.','Jeu.','Ven.','Sam.'),
					'dateFormat' => '%d $monthNames',
					'txt_noEvents' => "Pas d'évènement pour cette période",
					'txt_SpecificEvents_prev' => 'Evènements de ',
					'txt_SpecificEvents_prevDate' => 'Evènements du ',
					'txt_SpecificEvents_after' => '',
					'txt_next' => 'suivant',
					'txt_prev' => 'précédent',
					'txt_NextEvents' => 'Evènements à venir',
					'txt_GoToEventUrl' => "Voir l'évènement",
				),			
			'es' => array(
					'monthNames' => array( "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", 
							"Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ),
  					'dayNames' => array('Domingo','Lunes','Martes','Miércoles', 'Jueves','Viernes','Sabado'),
  					'dayNamesShort' => array('Dom','Lun','Mar','Mie', 'Jue','Vie','Sab'),
					'dateFormat' => '%d $monthNames',
					'txt_noEvents' => 'No hay eventos para este periodo',
					'txt_SpecificEvents_prev' => 'Eventos por ',
					'txt_SpecificEvents_prevDate' => 'Eventos en la ',
					'txt_SpecificEvents_after' => '',
					'txt_next' => 'siguiente',
					'txt_prev' => 'anterior',
					'txt_NextEvents' => 'Próximos eventos',
					'txt_GoToEventUrl' => 'Ir al evento',
				),
			);

    public static function onEventsList($name, $p)
    { 
    	$p=$p[0];
    	$languepreferee = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);

		//$lang = $languepreferee[0];
		$tmpLang = explode('-',$languepreferee[0]);
		$lang = $tmpLang[0];
		if (!isset(EventCalendar::$locale[$lang])) $lang='fr';
		
    	$resp = '';
		// Core CSS File. The CSS code needed to make eventCalendar works
		$resp .= '<link rel="stylesheet" href="manager/tools/eventCalendar/css/eventCalendar.css">';
		// Theme CSS file: it makes eventCalendar nicer
		$resp .= '<link rel="stylesheet" href="manager/tools/eventCalendar/css/eventCalendar_theme_responsive.css">';
		$resp .='<script src="manager/tools/eventCalendar/js/jquery.eventCalendar.js" type="text/javascript"></script>';
		$resp .= '<div id="eventCalendarList"></div>';
        $resp .= '<script type="text/javascript">
				    $(document).ready(function() {
				    
				        cal = $("#eventCalendarList");
				        if (cal) {
	        				$("#eventCalendarList").eventCalendar({
								eventsjson: "manager/tools/eventCalendar/json/event.humanDate.json.php",
        						jsonDateFormat:"human",
        						onlyFutureEventOnLoad: true,
        						eventsLimit: 6,
								cacheJson: false,
								showDescription: true,
								startWeekOnMonday: true,
								onlyOneDescription: false,
								showDescription: true,
        						eventsScrollable: true,
								monthNames: '.json_encode(EventCalendar::$locale[$lang]['monthNames']).',
								dayNames: '.json_encode(EventCalendar::$locale[$lang]['dayNames']).',
								dayNamesShort: '.json_encode(EventCalendar::$locale[$lang]['dayNamesShort']).',	
								dateFormat: "'.EventCalendar::$locale[$lang]['dateFormat'].'",
								txt_noEvents: "'.EventCalendar::$locale[$lang]['txt_noEvents'].'",
								txt_SpecificEvents_prev: "'.EventCalendar::$locale[$lang]['txt_SpecificEvents_prev'].'",
								txt_SpecificEvents_prevDate: "'.EventCalendar::$locale[$lang]['txt_SpecificEvents_prevDate'].'",
								txt_SpecificEvents_after: "'.EventCalendar::$locale[$lang]['txt_SpecificEvents_after'].'",
								txt_next: "'.EventCalendar::$locale[$lang]['txt_next'].'",
								txt_prev: "'.EventCalendar::$locale[$lang]['txt_prev'].'",
								txt_NextEvents: "'.EventCalendar::$locale[$lang]['txt_NextEvents'].'",
								txt_GoToEventUrl: "'.EventCalendar::$locale[$lang]['txt_GoToEventUrl'].'", 								
							});
				        
				        }
				        		
				    });
        	</script>';

        if ($p['return'])
        	return $resp;
        else
        	echo $resp;
    }
}

Hook::register('onEventCalendarList', 'EventCalendar', 'onEventsList');

?>