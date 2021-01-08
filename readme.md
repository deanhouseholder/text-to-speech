# Text-To-Speech using Google API

This script will allow you to synthesize text into an audio file using the Google Text-to-Speech API.

Please note, that while this script is open-source and free, the Google Text-to-Speech API is not. Also there are "Standard" voices which sound decent, as well as more premium "WaveNet" voices which if you select will cost you more. See [Google's Pricing page](https://cloud.google.com/text-to-speech/pricing).



## Installation

1. This script is PHP-based so you will need [PHP](https://php.net/) (CLI) and [Composer](https://getcomposer.org/).

Clone this git repo and in a terminal run:
```shell
composer install
```

2. Next you will need to generate a Google API credentials JSON file. Follow Steps 1-4 here:
https://cloud.google.com/text-to-speech/docs/quickstart-client-libraries

3. Save the .json file you downloaded in Step 4 into this code directory as `creds.json`.

4. Now you should be ready to run it. Open a terminal and run:
```sh
php tts.php "Hello world!"
```

5. Confirm that it doesn't complain about any errors. If not, it should have told you that it created a mp3 file in the current directory.



## Command-Line Options

There are several command-line options that allow you to configure the results.

Here's a list of the command-line arguments that the script supports:

```sh
php tts.php "text to speak" [output_filename] [rate] [voice] [format] [profile]
```

> **Note 1:** The square brackets around parameters in the example above represent optional arguments.<br>
> **Note 2:** Any of the parameters after `text to speak` can be left blank to select the default, but you must use quotes to reserve the spot. For example, if you want to change the `format` without changing the `rate` or `voice` you can run:
>
> ```shell
> php tts "text to speak" myfile.wav "" "" wav
> ```



| Option                 | Description                                                  |
| ---------------------- | ------------------------------------------------------------ |
| "text to speak" or "-" | Text that you wish to synthesize into audio. If you wish to pipe text into the script please use a dash "-" instead. *(Quotes are not necessary)* |
| output_filename        | The name of the file you would like it to create. If left blank, the default filename format is:<br />`YYYY-MM-DD_HH-MM-SS.mp3`. |
| rate                   | This is the speed of playback. The default is `1.5` (meaning `1.5` times faster than normal speaking speed) but this is adjustable between `0.25` and `4.0`. |
| voice                  | The name of a supported Google API voice. The default is `en-US-Standard-I`. For a full list, refer to https://cloud.google.com/text-to-speech/docs/voices. |
| format                 | The output file format you wish to use. The options are: `wav`, `mp3`, or `ogg`. The default is `mp3`. *(Note: it is possible to specify a different format than the `output_filename` above so to avoid confusion, keep these in sync.)* |
| profile                | The sound profile of the target device you wish to use. The default is handset-class-device. For a full list, refer to: https://cloud.google.com/text-to-speech/docs/audio-profiles |



### Full syntax example:

```shell
php tts.php "This text will be synthesized to a wav file" my-text.wav 1.25 en-US-Standard-C wav medium-bluetooth-speaker-class-device
```



## Options for Passing in Text to be Synthesized

### To pipe in content, use a single dash as the first argument. Run:
```shell
echo "text to speak" | php tts.php - [output_filename] [rate] [voice] [format] [profile]
```



### Text size constraints

The amount of text the command-line can receive varies from 32k to 2mb+. That said, you may hit this limit or you may prefer to pass in a reference to a file. Use the following syntax to accomplish this.

#### To pass in the contents of a file, use a single dash as the first argument. Run one of:

```shell
cat /path/to/file | php tts.php - [output_filename] [rate] [voice] [format] [profile]
```

or

```shell
php tts.php - [output_filename] [rate] [voice] [format] [profile] </path/to/file
```



### Optional execution syntax

If you are using a Linux/Unix-based prompt this script can be run directly without invoking PHP on the command line every time.

So instead of running:
```shell
php tts.php
```

You can instead run:
```shell
./tts.php
```

> Note: The default permissions from a git clone should've left the `tts.php` file executable. If it is no longer executable, run:
>
> ```shell
> chmod u+x tts.php
> ```

