/* --- encoding: utf-8 --- */
==========================================================================
Plume CMS plugin for SimplePie - v1.0RC1
==========================================================================

###-----------[ ToC ]-----------###

Changelog...........................cf. line [13]
About...............................cf. line [19]
English documentation...............cf. line [26]
Documentation française.............cf. ligne [176]

###-----------[ Changelog ]-----------###

** 2007-02-23 **
[+] Released v1.0-RC1
[+] Documentation written, based on the WP plugin instructions

###-----------[ About ]-----------###

Plugin homepage : http://beta.technalogie.info/wiki/doku.php?id=plume-cms:plugins:simplepie
Author : Cécilia Gaudard
Contact : cilyia {at} gmail {dot} com

#####################################################
###-----------[ English documentation ]-----------###

SimplePie is a very fast and easy-to-use class, written in PHP, for
reading RSS and Atom syndication feeds. It is licensed under the LGPL.

This plugin is largely inspired from the WordPress plugin for SimplePie.

1. Installation
================

1. Download the the archive.
2. Unpack it on your computer. You should keep the folder tree as it is.
3. Upload the simplepie folder into the /manager/tools folder.

1.1. Folder tree
------------------

*  /simplepie
    ** /simplepie_cache (The folder is actually empty. If you intend
                         to rename it, you should modify the register.php
                         file at line 64
    ** simplepie.inc
    ** register.php 

2. Utilisation
================

Using SimplePie is pretty simple, it's just a function to add into your
template files, at the exact place you want the feed to be displayed.
The function accepts handy parameters, for your own pleasure.

A basic example of the function to add into you template file would be :

#-----[code]
 <?php echo pxGetFeed('http://example.com/feed.xml'); ?>
#-----[/code]

"http://example.com/feed.xml" would be the feed URI.

2.1. Parameters
----------------

The function takes 2 parameters, the first is a simple one, the latter
is a complex one. The function model is :

#-----[code]
<?php echo pxGetFeed($url, $renderer); ?>
#-----[/code]

2.1.1. $url parameter
----------------------
The first parameter is the feed URL. Only passing this will display the
default way:
    * An <h3> containing the feed's title, linked back to 
       the originating site.
    * An ordered list, containing all of the news items in the feed.
    * The news item's title, linked back to the originating post.
    * The full HTML description for each news item.

2.1.2. $renderer parameter
---------------------------
The second parameter contains a series of keywords and values. Each
keyword:value set is separated with a pipe. The keyword and value are
separated with a colon. For example:

#-----[code]
<?php
   echo pxGetFeed('http://example.com/feed.xml', 'items:5|shortdesc:200|showdate:j M Y');
?>
#-----[/code]

The supported keyworks are :

items:(int)
    Limits the number of items returned. If you set this value to 5,
    then you'll get back the 5 most recent posts. If there's a feed with
    fewer than 5 posts, SimplePie will return all of them. Defaults to all.

showdesc:(bool)
    Determines whether the description should be shown or not. If set to
    false, descriptions are omitted, and the ordered list will display
    only the linked item titles with no special formatting. Defaults to
    true.

showdate:(string)
    Displays the date of the news item. Accepts anything that's allowed
    in PHP's date() function [see http://fr.php.net/date]. Defaults to
    blank.

shortdesc:(int)
    Strips all tags from the item's description and limits the number of
    characters that are displayed. Accepts any numeric value. If more
    characters are allowed than are in the description, the entire
    description will be displayed. If the text wasn't cut at the end of
    a sentence (ending with a period, exclamation point, or question
    mark), an ellipsis will be added to the end of the text. Defaults to
    all characters.

showtitle:(bool)
    Determines whether the built-in feed title is displayed or not.
    Defaults to true.

alttitle:(string)
    Défini un titre personnalisé pour le flux, à la place du titre
    originel.

error:(string)
    Displays a custom error message for when there is a problem
    retrieving the feed. Defaults to the standard error messages.


3. Style & Markup preview
==========================

If you want to apply special CSS styles to the feed display, here's some
basic markup that represents what is generated.

#-----[code]
<div class="simplepie">
    <h3><a href="http://example.com">Example Site</a></h3>
    <ol>
        <li>
            <strong>
                <a href="..." title="Item Title 1">
                    Item Title 1
                </a>
                <span class="date">
                    29 May 2006
                </span>
            </strong>
            <br />
            The description for the item.
        </li>
        <li>
            <strong>
                <a href="..." title="Item Title 2">
                    Item Title 2
                </a>
                <span class="date">
                    29 May 2006
                </span>
            </strong>
            <br />
            The description for the item.
        </li>
    </ol>
</div>
#-----[/code]

#######################################################
###-----------[ Documentation française ]-----------###

SimplePie est une API permettant l’intégration de flux RSS et ATOM
dans des pages Web et répondant aux termes de la licence LGPL.

Le plugin ici présenté est entièrement basé sur cette API, ou
très largement inspiré de l’utilisation qui en est faite dans son
adaptation pour WordPress, devrais-je plutôt dire. 


1. Installation
================

1. Télécharger l’archive.
2. Décompacter l’archive sur votre ordinateur. 
    Faites en sorte de garder l’arborescence intact.
3. Effectuer un upload du dossier simplepie obtenu à l’étape précédente
    sur votre espace Web. Vous devez le placer dans le dossier
    /manager/tools. 

1.1. Arborescence
------------------

*  /simplepie
    ** /simplepie_cache (Le dossier est effectivement vide. 
                         Si vous changez son nom, reportez-vous 
                         à la ligne 64 de register.php.)
    ** simplepie.inc
    ** register.php 

2. Utilisation
================

Le principe de SimplePie est simple, c’est une fonction à ajouter
à vos gabarits, à l’endroit précis où vous voulez afficher le flux.
La fonction en question permet un paramétrage élaboré offrant une
possibilité d’intégration fine.

Vous devrez donc insérer la fonction suivante dans le ou les gabarits :

#-----[code]
 <?php echo pxGetFeed('http://example.com/feed.xml'); ?>
#-----[/code]

"http://example.com/feed.xml" est l’adresse du flux à insérer.

Notez que ce code est la version "basique". Comme évoqué précédemment,
vous pouvez affiner le rendu via des paramètres. 


2.1. Paramètres
----------------

La fonction accepte accepte deux paramètres, l’un simple, l’autre
composé, basé sur ce modèle :

#-----[code]
<?php echo pxGetFeed($url, $renderer); ?>
#-----[/code]

2.1.1. Le paramètre simple $url
-------------------------------
Ce paramètre représente l’URL du flux à insérer. Passer ce paramètre
uniquement est tout à fait possible. Le rendu sur votre page sera alors
du type :
  * Une balise <h3> contiendra le titre du flux, et un lien vers son
     site d’origine ;
  * Une liste ordonnée <ol> contiendra tous les messages, ou items, 
     du flux ;
  * Le titre de chaque item sera lié vers son message d’origine ;
  * La description complète au format HTML de chaque item sera affichée. 

2.1.2. Le paramètre composé $renderer
--------------------------------------
Ce second paramètre contient une série de couples mots-clés/valeurs. 
Ces couples sont séparés par le caractère '|' (pipe en anglais), et 
les mots-clés sont séparés de leurs valeurs par le caractères ':' 
(les deux points). Par exemple :

#-----[code]
<?php
   echo pxGetFeed('http://example.com/feed.xml', 'items:5|shortdesc:200|showdate:j M Y');
?>
#-----[/code]

Voici la liste des mots-clés disponibles :

items:(entier)
    Défini le nombre d’items à afficher. Si la valeur indiquée est
    supérieure au nombre d’items contenu dans le flux, tous les items
    seront affichés. Par défaut, il affichera tous les items.

showdesc:(booléen)
    Détermine si la description doit être affichée ou non. 
    Si la valeur est mise à 'false', les descriptions sont omises et 
    la liste ordonnée <ol> rendra uniquement un lien sur les titres 
    sans format spécifique. Par défaut, la valeur est à 'true'.

showdate:(chaîne)
    Retourne le format de la date. Le formatage est géré de le même
    façon que la fonction PHP 'date()'. Par défaut, le format de date
    appliqué est 'j F Y, g:i a'.

shortdesc:(entier)
    Dénue la description des items de tout formatage HTML et limite le
    nombre des caractères à afficher. Ce paramètre accepte toute valeur
    numérique. Si la description contient moins de caractères que
    définie, elle sera affichée en entier. Si le texte n’a pas été coupé
    à la fin d’une phrase (finissant par un point, un point 
    d’exclamation ou d’interrogation), une ellipse - ou points de
    suspension, seront ajoutés en fin de texte. Par défaut, la valeur
    est mise à tous les caractères.

showtitle:(booléen)
    Détermine si le titre originel du flux est affiché ou non. Par
    défaut, la valeur est à 'true'.

alttitle:(chaîne)
    Défini un titre personnalisé pour le flux, à la place du titre
    originel.

error:(chaîne)
    Défini un message d’erreur personnalisé lorsque le flux ne peut 
    être récupéré. Par défaut, ce sont les messages d’erreurs standards
    qui sont retournés. 

3. Aperçu du balisage HTML
===========================

Voici un aperçu rapide du rendu de la fonction dans son paramétrage par
défaut :

#-----[code]
<div class="simplepie">
    <h3><a href="http://example.com">Example Site</a></h3>
    <ol>
        <li>
            <strong>
                <a href="..." title="Item Title 1">
                    Item Title 1
                </a>
                <span class="date">
                    29 May 2006
                </span>
            </strong>
            <br />
            The description for the item.
        </li>
        <li>
            <strong>
                <a href="..." title="Item Title 2">
                    Item Title 2
                </a>
                <span class="date">
                    29 May 2006
                </span>
            </strong>
            <br />
            The description for the item.
        </li>
    </ol>
</div>
#-----[/code]