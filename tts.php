#!/usr/bin/env php
<?php
/*
 * Text-To-Speech using Google API
 *
 * Pass in arguments via the command line. Only the first is required. Run:
 * php tts.php "text to speak" [output_filename] [rate] [voice] [format] [profile]
 *
 * See readme.md for more details.
 *
 * Note: This script requires php-bcmath extension to be installed
 *
 */

require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . 'creds.json');


// **********************
// * Configure Settings *
// **********************

// Define defaults
define('DEFAULT_FILE',    date('Y-m-d_H-i-s') . '.mp3');
define('DEFAULT_RATE',    '1.5');
define('DEFAULT_VOICE',   'en-US-Standard-I');      // Options: https://cloud.google.com/text-to-speech/docs/voices
define('DEFAULT_FORMAT',  '2');                     // 1=wav, 2=mp3, 3=ogg
define('DEFAULT_PROFILE', 'handset-class-device');  // Options: https://cloud.google.com/text-to-speech/docs/audio-profiles
define('TIMEOUT_SECS',    '2');

// Get input text
if (isset($argv[1]) && !empty($argv[1])) {
    if ($argv[1] == '-h' || $argv[1] == '--help') {
        die("\nSee readme.md for help\n\n");
    } elseif ($argv[1] == '-') {
        // Get input text from STDIN (pipe)
        $text = '';
        $fh = fopen('php://stdin', 'r');
        $read = [$fh];
        $write = null;
        $except = null;
        if (stream_select($read, $write, $except, TIMEOUT_SECS) === 1) {
            while ($line = fgets($fh)) {
                $text .= $line;
            }
        }
        fclose($fh);
    } else {
        // Get input text from argument
        $text = $argv[1];
    }
}
if (empty($text)) {
    die("\nError: No content passed in\n\n");
}

// Get output filename
if (isset($argv[2]) && !empty($argv[2])) {
    $output_file = $argv[2];
} else {
    $output_file = DEFAULT_FILE;
}

// Get input speaking rate
if (isset($argv[3]) && !empty($argv[3])) {
    $output_rate = $argv[3];
} else {
    $output_rate = DEFAULT_RATE;
}

// Get input voice name
if (isset($argv[4]) && !empty($argv[4])) {
    $voice_name = $argv[4];
} else {
    $voice_name = DEFAULT_VOICE;
}

// Get output format
// Format must be an integer, but allow passing in case-insensitive strings: 1=wav, 2=mp3, 3=ogg
if (isset($argv[5]) && !empty($argv[5])) {
    $output_format = $argv[5];
    if (!is_integer($output_format)) {
        if (strtolower($output_format) == "wav") {
            $output_format = 1;
        } elseif (strtolower($output_format) == "mp3") {
            $output_format = 2;
        } elseif (strtolower($output_format) == "ogg") {
            $output_format = 3;
        } else {
            die("\nError: Could not recognize output format.\n\nPlease specify one of:\nwav\nmp3\nogg\n\n");
        }
    }
} else {
    $output_format = DEFAULT_FORMAT;
}

// Get output profile
if (isset($argv[6]) && !empty($argv[6])) {
    $output_profile = $argv[6];
} else {
    $output_profile = DEFAULT_PROFILE;
}

$language = substr($voice_name, 0, 5);


// ******************
// * Set up Objects *
// ******************

// Instantiate the main text-to-speech client
$tts_client = new TextToSpeechClient();

// Pass in text to synthesize
$synthesis_input = new SynthesisInput();
$synthesis_input->setText($text);

// Build the voice request
$voice_request = new VoiceSelectionParams();
$voice_request->setLanguageCode($language);
$voice_request->setName($voice_name);

// Configure the type of audio file you want returned
$audio_config = new AudioConfig();
$audio_config->setAudioEncoding($output_format);
$audio_config->setEffectsProfileId([$output_profile]);
$audio_config->setSpeakingRate($output_rate);


// ***********************
// * Generate Audio File *
// ***********************

// Generate audio file
try {
    $response = $tts_client->synthesizeSpeech($synthesis_input, $voice_request, $audio_config);
    file_put_contents($output_file, $response->getAudioContent());
} catch (Exception $e) {
    $json = $e->getMessage();
    $message = json_decode($json, true);
    die("\nCould not call text-to-speech API:\n\nError received:\n" . $message['message'] . "\n\n");
}

echo "Audio content written to $output_file\n";
