<?php
/* -*- tab-width: 4; indent-tabs-mode: nil; c-basic-offset: 4 -*- */
/*
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Plume CMS, a website management application.
# Copyright (C) 2001-2006 Loic d'Anterroches and contributors.
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

/**
 * Generate multipart emails.
 *
 * Class to easily generate multipart emails. It supports embedded
 * images within the email. It can be used to send both a text version 
 * and the HTML equivalent version of a message.
 *
 * The encoding of the message is utf-8 by default.
 *
 * Usage example:
 * <code>
 * $email = new Plume_Mail('from_email@example.com', 'to_email@example.com', 
 *                        'Subject of the message');
 * $img_id = $email->addAttachment('/var/www/html/img/pic.jpg', 'image/jpg');
 * $email->addMessage('<html><head></head><body>'."\n"
 *         .'This is text before <img src="cid:'.$img_id.'"> and after.'."\n"
 *         .'</body></html>', 
 *                    'text/html');
 * $email->sendMail(); 
 * </code>
 *
 * @credits krisdover on http://www.php.net/manual/en/function.mail.php
 * @credits umu on http://www.php.net/manual/en/function.imap-8bit.php
 */
class Plume_Mail
{
    var $header;
    var $parts;
    var $message;
    var $subject;
    var $to_address;
    var $boundary;
    var $encoding = 'utf-8';
    
    /**
     * Delimitation character of the headers.
     */
    var $hd = "\n";

    /**
     * Extra headers.
     *
     * Associative array of extra headers to add to the message.
     */
    var $headers = array();

    /**
     * Construct the base email.
     *
     * FIXME: To provide a document as text and an alternative as
     * HTML, the content type should be multipart/alternative with one
     * plain/text and one HTML document. An option somewhere should
     * enable this option.
     *
     * @param string The email of the sender.
     * @param string The destination email.
     * @param string The subject of the message.
     * @param string Encoding of the message ('utf-8)
     */
    function Plume_Mail($src, $dest, $subject, $encoding='utf-8')
    {
         $this->to_address = $dest;
         $this->subject = $subject;
         $this->parts = array();
         $this->boundary = '------------' . md5(uniqid(time()));
         $this->encoding = 'utf-8';
         $this->header = 'From: '.$src.$this->hd
             .'MIME-Version: 1.0'.$this->hd
             .'Content-Type: multipart/related;'."\n" 
             .'              boundary="'.$this->boundary.'"'.$this->hd
             .'X-Mailer: Plume CMS - http://plume-cms.net/';
     }

    /**
     * Add the base plain text message to the email.
     *
     * @param string The message
     * @param string The mime-type ('text/plain;')
     */
    function addMessage($msg='', $ctype='text/plain')
    {
        // Base message is always the first element.
        array_unshift($this->parts,
                      'Content-Type: '.$ctype.'; charset='.$this->encoding
                      ."\n\n".$msg);
    }

    /**
     * Add an attachment to the message.
     *
     * The file to attach must be available on disk and you need to
     * provide the mimetype of the attachment manually.
     *
     * The id of the attachment can be used for embedding images in
     * HTML emails. Avoid abusing the use of them or your emails will
     * be flagged as spam.
     *
     * @param string Path to the file to be added.
     * @param string Mimetype of the file to be added.
     * @return string The id of the attachment.
     */
     function addAttachment($file, $ctype){
         $fname = basename($file);
         $data = file_get_contents($file);
         $i = count($this->parts);
         $content_id = 'part'.$i.sprintf('%09d', crc32($fname))
             .strrchr($this->to_address, '@');
         $this->parts[$i] = 'Content-Type: '.$ctype.'; name="'.$fname."\n" 
             .'Content-Transfer-Encoding: base64'."\n"
             .'Content-ID: <'.$content_id.'>'."\n"
             .'Content-Disposition: inline;'."\n"
             .'                     filename="'.$fname."\n\n".
             chunk_split(base64_encode($data), 68, "\n");
         return $content_id;
     }

    /**
     * Generate the message.
     */
    function buildMessage()
    {
        $this->message = 'This is a multipart message in mime format.'."\n";
        foreach ($this->parts as $part) {
            $this->message .= '--'.$this->boundary."\n".$part."\n";
        }
        $this->message .= '--'.$this->boundary.'-- '."\n";
    }

    /**
     * Get the message body as a string.
     *
     * @return string Message body
     */
    function getMessage()
    {
        $this->buildmessage();
        return $this->message;
    }

    /**
     * Effectively sends the email.
     */
    function sendMail(){
        $this->buildmessage();
        mail($this->to_address, $this->subject, $this->message, $this->header);
    }

    /**
     * Will be used when allowing additional headers.
     *
     * http://www.php.net/manual/en/function.imap-8bit.php
     */
    function quoted_printable_encode($text) {
        // split text into lines
        $lines = explode(chr(13).chr(10), $text);
        for ($i=0; $i<count($lines); $i++) {
            $line =& $lines[$i]; // $line is modified by reference
            if (strlen($line)===0) {
                continue; // do nothing, if empty
            }
            $reg_exp = '/[^\x20\x21-\x3C\x3E-\x7E]/e';
            $replace = 'sprintf( "=%02X", ord ( "$0" ) ) ;';
            $line = preg_replace($reg_exp, $replace, $line); 

            // encode x09,x20 at lineends
            $length = strlen($line);
            $last_char = ord($line{$length-1});

            // imap_8_bit does not encode x20 at the very end of a text,
            // here is, where I don't agree with imap_8_bit,
            // please correct me, if I'm wrong,
            // or comment next line for RFC2045 conformance, if you like
            if ($i != count($lines)-1) {
                if (($last_char==0x09)||($last_char==0x20)) {
                    $line{$length-1}='=';
                    $line .= ($last_char==0x09) ? '09' : '20';
                }
            }
            $line=str_replace(' =0D', '=20=0D', $line);
            // finally split into softlines no longer than 76 chars,
            // for even more safeness one could encode x09,x20
            // at the very first character of the line
            // and after soft linebreaks, as well,
            // but this wouldn't be caught by such an easy RegExp                 
            preg_match_all('/.{1,73}([^=]{0,2})?/', $line, $match);
            $line = implode('='.chr(13).chr(10), $match[0]); // add soft crlf's

        }
        // join lines into text
        return implode(chr(13).chr(10), $lines);
    }
}
?>
