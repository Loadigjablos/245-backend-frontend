<?php

$from_date = "2023-03-07 21:43:00";
$to_date = "2023-03-07 21:55:00";
$place_name = "Rubin";
$description = "School";

// Convert date and time to UTC format
$from_date_utc = gmdate('Ymd\THis\Z', strtotime($from_date));
$to_date_utc = gmdate('Ymd\THis\Z', strtotime($to_date));

// Generate unique ID for the event
$uid = uniqid();

// Generate .ics file contents
$ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
UID:" . $uid . "
DTSTAMP:" . $from_date_utc . "
DTSTART:" . $from_date_utc . "
DTEND:" . $to_date_utc . "
SUMMARY:" . $place_name . "
END:VEVENT
END:VCALENDAR";

// Send email with .ics file contents as the email body
$to = "mouayad.alnhlawe@ict.csbe.ch";
$subject = "Event Reservation";
$message = "Please find below the event reservation details in iCalendar format:\r\n\r\n";
$headers = "From: morhaf.mouayad@gmail.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/calendar; charset=utf-8; method=REQUEST\r\n";
$body = $message . $ical;

if (mail($to, $subject, $body, $headers, "-r morhaf.mouayad@gmail.com")) {
    echo "Email sent with iCalendar file.";
} else {
    echo "Email sending failed.";
}
