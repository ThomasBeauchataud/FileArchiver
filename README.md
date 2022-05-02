# FileArchiver

FileArchiver is a library to manage files archiving as well as their rotation and their recovery

## Table of content

- [Archivage](#archivage)
- [Suppression and rotation](#suppression-and-rotation)
- [Recovery](#recovery)

## Archivage

Let's suppose we have a file named `demo.txt` which we want to archive for 14 days

```
$fileArchiver = new FileArchiver();

...

$filePath = './demo.txt';
$archivedFile = $fileArchiver->archive($filePath, new DateInterval('P14D'));
```

And that's it. Your file is archived in the default storage directory `__DIR__` but you can specify another location is
you want

```
$fileArchiver = new FileArchiver('/my/storage/directory');
```

> If you are using Symfony, the default storage location is `%kernel.project_dir%/var/archive/`

## Suppression and rotation

The archive rotation is proceeded each time you archive a file with the same mask.

For exemple let's imagine we archived our file `demo.txt` 2 hours ago for a duration of 1 jour. When we will archive our
new file, the previous archived will be deleted because he overlaps his archive duration

> The file rotation on archive affect only files with the same filename. To clear all expired archives when a new file
> is archived, you have to change the second argument constructor as below
> ```
> $fileArchiver = new FileArchiver('/my/storage/directory', false);
> ```

You can anyway clear the archived files the way you want by calling the clear method
```
// This will clear all file with the mask 'demo*' having an expired archive duration
$fileArchiver->clear('demo');

// This will clear all file with the mask '*' archived before the passed date
// In the exemple below it will clear all archived files
$fileArchiver->clear('', new DateTime());
```

## Recovery

You can recover archived files as below and obtain the list of archived files with the given mask
```
$archivedFilesPath = $fileArchiver->find('demo');
```