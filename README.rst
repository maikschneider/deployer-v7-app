deployer-extended
=================

    .. image:: https://scrutinizer-ci.com/g/sourcebroker/deployer-extended/badges/quality-score.png?b=master
        :target: https://scrutinizer-ci.com/g/sourcebroker/deployer-extended/?branch=master

    .. image:: http://img.shields.io/packagist/v/sourcebroker/deployer-extended.svg?style=flat
        :target: https://packagist.org/packages/sourcebroker/deployer-extended

    .. image:: https://img.shields.io/badge/license-MIT-blue.svg?style=flat
        :target: https://packagist.org/packages/sourcebroker/deployer-extended

.. contents:: :local:

What does it do?
----------------

Library with some additional tasks for deployer (deployer.org).

Setting's documentation
------------------------

composer_version
~~~~~~~~~~~~~~~~

Install specific composer version. Use tags. Valid tags are here https://github.com/composer/composer/tags . Default
value is ``null``.


composer_channel
~~~~~~~~~~~~~~~~

Install latest version from channel. Set this variable to '1' or '2' (or 'stable', 'snapshot', 'preview'). Read more on composer docs.
Default value is ``stable`` which will install latest version of composer. If you need stability set it better to '1' or '2'.

composer_channel_autoupdate
~~~~~~~~~~~~~~~~~~~~~~~~~~~

If set then on each deploy the composer is checked for latest version according to ``composer_channel`` settings.
Default value is ``true``.

web_path
~~~~~~~~

Path to public when not in root of project. Must be like "pub/" so without starting slash and with ending slash.


Task's documentation
--------------------

buffer
~~~~~~

buffer:start
++++++++++++

Starts buffering requests to application entrypoints. Application entrypoints means here any php file that
can handle HTTP requests or handle CLI calls. For most good frameworks there is only few entrypoints.

The request are buffered but at the same time if you set special http header (by default HTTP_X_DEPLOYER_DEPLOYMENT)
with special value you will be able to make regular request. This can be very handy to check if the application
is working at all after switch (symlink to current) and to warm up some caches.

When you run `buffer:stop`_ all the waiting requests will hit the http server (or cli entrypoint).

The entrypoints are taken from variable "buffer_config" which is array of entrypoints configurations.

Options:

- | **entrypoint_filename**
  | *required:* yes
  |
  | The filename that will be overwritten with "entrypoint_inject" php code. If entrypoint is inside folder then
    write it with this folder like: 'entrypoint_filename' => 'index.php'

  |
- | **entrypoint_needle**
  | *required:* no
  | *default value:* <?php
  |
  | A "needle" in "entrypoint_filename" after which the php code from "entrypoint_inject" will be injected.

  |
- | **entrypoint_refresh**
  | *required:* no
  | *default value:* 200000 μs (200ms)
  |
  | How often the entrypoint will recheck if ``.flag.requestbuffer`` is still there. Values in microseconds.
  | 100000 μs = 100 ms = 0,1 s.
  |

- | **entrypoint_inject**
  | *required:* no
  |
  | A php code that actually do the buffering.
  | The default code with already prefilled variables (random, locker_filename, locker_expire, entrypoint_refresh):
  ::

      isset($_SERVER['HTTP_X_DEPLOYER_DEPLOYMENT']) && $_SERVER['HTTP_X_DEPLOYER_DEPLOYMENT'] == 'af37fd227cb6429c211168666dd28391' ? $deployerExtendedEnableBufferLo
      isset($_ENV['DEPLOYER_DEPLOYMENT']) && $_ENV['DEPLOYER_DEPLOYMENT'] == 'af37fd227cb6429c211168666dd28391' ? $deployerExtendedEnableBufferLock = false: $deployerExtendedEnableBufferLock = true;
      clearstatcache(true, __DIR__ . '/.flag.requestbuffer');
      while (file_exists(__DIR__ . '/.flag.requestbuffer') && $deployerExtendedEnableBufferLock) {
          usleep(200000);
          clearstatcache(true);
          if(time() - @filectime(__DIR__ . '/.flag.requestbuffer') > 60) @unlink(__DIR__ . '/.flag.requestbuffer');
      }


- | **locker_filename**
  | *required:* no
  | *default value:* .flag.requestbuffer
  |
  | When file with name ".flag.requestbuffer" exists the requests are buffered. The task `buffer:stop`_ just removes
    the ".flag.requestbuffer" files without removing the "entrypoint_inject" code.
  |

- | **locker_expire**
  | *required:* no
  | *default value:* 60
  |
  | The time in seconds after which the .flag.requestbuffer files will be removed automatically.
  |
  | Usually its buffer:stop task that should remove ".flag.requestbuffer" file. Unfortunately sometimes deploy can fail.
  | If deploy will fail after buffer:start task and before buffer:stop then the ".flag.requestbuffer" will be automatically removed
  | anyway after "locker_expire" time.

The simplest configuration example:
::

   set('buffer_config', [
           'index.php' => [
               'entrypoint_filename' => 'index.php',
           ]
       ]
   );

More entrypoints example. An example for CMS TYPO3 8.7 LTS:
::

   set('buffer_config', [
           'index.php' => [
               'entrypoint_filename' => 'index.php', // frontend
           ]
           'typo3/index.php' => [
               'entrypoint_filename' => 'typo3/index.php', // backend
           ],
           'typo3/cli_dispatch.phpsh' => [
               'entrypoint_filename' => 'typo3/cli_dispatch.phpsh', // cli
           ]
       ]
   );

More configuration options examples:
::

   set('buffer_config', [
           'index.php' => [
               'entrypoint_filename' => 'index.php',
               'entrypoint_needle' => '// inject php code after this comment',
               'locker_filename' => 'deployment.lock',
               'entrypoint_inject' => 'while (file_exists(__DIR__ . ".flag.requestbuffer")){' . "\n"
                                      . 'usleep(200000);' . "\n"
                                      . 'clearstatcache(true, __DIR__ . "/.flag.requestbuffer")' . "\n"
                                      . '}'
           ]
       ]
   );


buffer:stop
+++++++++++

Stop buffering requests to application entrypoints. It deletes ".flag.requestbuffer" files.

deploy
~~~~~~

deploy:check_branch
+++++++++++++++++++

Check if the branch you want to deploy is different from the branch currently deployed on host. If you have information that
the branch on the host is different than the branch you want to deploy then you can take decision to overwrite it or not.
For this task to work you need also to run task `deploy:extend_log`_, which will store info about last deployed branch.

deploy:check_branch_local
+++++++++++++++++++

Check if the branch you are currently checked out on your local is the same branch you want to deploy.
The ``deploy.php`` files on both branches can be different and that can influence the deploy process.

deploy:check_composer_install
+++++++++++++++++++++++++++++

Check if there is composer.lock file on current instance and if its there then make dry run for
"composer install". If "composer install" returns information that some packages needs to be updated
or installed then it means that probably developer pulled composer.lock changes from repo but forget
to make "composer install". In that case deployment is stopped to allow developer to update packages,
make some test and make deployment then.

deploy:check_lock
+++++++++++++++++

Checks for existence of file deploy.lock in root of current instance. If the file deploy.lock is there then
deployment is stopped.

You can use it for whatever reason you have. Imagine that you develop css/js locally with "grunt watch".
After you have working code you may forget to build final js/css with "grunt build" and you will deploy
css/js that will be not used on production which reads compiled css/js.

To prevent this situation you can make "grunt watch" to generate file "deploy.lock" (with text "Run
'grunt build'." inside) to inform you that you missed some step before deploying application.

deploy:extend_log
+++++++++++++++++

Log info about deployed branch / tag / hash and the user who deployed. Log is stored in ``.dep/releases.extended`` file.

file
~~~~
file\:backup
++++++++++++

Creates backup of files.
Single task may perform multiple archivization using defined filters.
Old ones are deleted after executing this task. Default limit is 5.

Configuration description

- | **file_backup_packages**
  | *required:* yes
  | *default value:* none
  | *type:* array
  |
  | Packages definition

- | **file_backup_keep**
  | *required:* no
  | *default value:* 5
  | *type:* int
  |
  | Limit of backups per package

Sample configuration:
::

    set('file_backup_packages', [
        'config' => [
            '-path "./etc/*"',
        ],
        'translations' => [
            '-path "./l10n/*"',
            '-path "./modules/*/l10n/*"',
        ],
        'small_images' => [
            [ '-path "./media/uploads/*"', '-size -25k' ],
            [ '-path "./media/theme/*"', '-size -25k' ],
        ],
    ]);

    set('file_backup_keep', 10);

Config variable *file_backup_packages* stores information about backup packages and files filtering options.
Each package defines filters which will be used in `find` command.
First level element are groups which will be concatenated using logical alternative operator operator OR.
If group is array type then group elements will be concatenated using logical conjunction operator.

Package *config*:
It is simplest definition.
For this package all files from directory "./etc/" will be backuped.

Package *translations*:
For this one all files from directory "./l10n/" will be backuped.
It will also include files from all "l10n/" from "modules" subdirectory.
For example "modules/cookies/l10n"

Package *small_images*:
This one will contain all small (smaller than 25kB) files from "media/uploads" and "media/theme".

As you can see *file_backup_keep* is set to 10 which means only newest 10 backups per package will be stored.


file:copy_dirs_ignore_existing
++++++++++++++++++++++++++++++

Copy directories from previous release except for those directories which already exists in new release.

file:copy_files_ignore_existing
+++++++++++++++++++++++++++++++

Copy files from previous release except for those files which already exists in new release.


file\:rm2steps\:1
+++++++++++++++++

Allows to remove files and directories in two steps for "security" and "speed".

**Security**

Sometimes removing cache folders with lot of files takes few seconds. In meantime of that process a new frontend
request can hit http server and new file cache will start to being generated because it will detect that some cache
files are missing and cache needs to be regenerated. A process which is deleting the cache folder can then delete
the newly generated cache files. The output of cache folder is not predictable in that case and can crash
the application.

**Speed**

If you decide to remove the cache folder during the `buffer:start`_ then its crucial to do it as fast as possible in
order to buffer as low requests as possible.


The solution for both problems of "security" and "speed" is first rename the folder to some temporary and then delete it
later in next step. Renaming is atomic operation so there is no possibility that new http hit will start to build cache
in the same folder. We also gain speed because we can delete the folders/files at the end of deployment with task
`file:rm2steps:2`_ if that's needed at all because deployer "cleanup" task will remove old releases anyway.


file\:rm2steps\:2
+++++++++++++++++

The second step of file:rm2steps tandem. Read more on `file:rm2steps:1`_

cache
~~~~~

cache:clear_php_cli
+++++++++++++++++++

This task clears the file status cache, opcache and eaccelerator cache for CLI context.

cache:clear_php_http
++++++++++++++++++++

This task clears the file status cache, opcache and eaccelerator cache for HTTP context. It does following:

1) Creates file "cache_clear_[random].php" in "{{deploy_path}}/current" folder.
2) Fetch this file with selected method - curl / wget / file_get_contents - by default its wget.
3) The file is not removed after clearing cache for reason. It allows to prevent problems with realpath_cache. For
   more info read http://blog.jpauli.tech/2014-06-30-realpath-cache-html/

You must set **public_urls** configuration variable so the script knows the domain it should fetch the php script.
Here is example:

::

  server('prelive', 'example.com', 22)
    ->user('deploy')
    ->stage('prelive')
    ->set('deploy_path', '/home/web/html/www.example.com.prelive')
    ->set('public_urls', ['https://prelive.example.com']);


Task configuration variables:

- | **cache:clear_php_http:phpcontent**
  | *required:* no
  | *type:* string
  | *default value:*
  ::

    <?php
      clearstatcache(true);
      if(function_exists('opcache_reset')) opcache_reset();
      if(function_exists('eaccelerator_clear')) eaccelerator_clear();

  |
  | Php content that will be put into dynamically created file that should clear the caches.
  |

- | **public_urls**
  | *required:* yes
  | *default value:* none
  | *type:* array
  |
  | Domain used to prepare url to fetch clear cache php file. Its expected to be array so you can put there more than one
    domain and use it for different purposes but here for this task the first domain will be taken.
  |

- | **fetch_method**
  | *required:* no
  | *default value:* wget
  | *type:* string
  |
  | Can be one of following value:
  | - curl,
  | - wget,
  | - file_get_contents
  |

- | **cache:clear_php_http:timeout**
  | *required:* no
  | *default value:* 15
  | *type:* integer
  |
  | Set the timeout in seconds for fetching php clear cache script.
  |

- | **local/bin/curl**
  | *required:* no
  | *default value:* value of "which curl"
  | *type:* string
  |
  | Path to curl binary on current system.
  |

- | **local/bin/wget**
  | *required:* no
  | *default value:* value of "which wget"
  | *type:* string
  |
  | Path to wget binary on current system.
  |

- | **local/bin/php**
  | *required:* no
  | *type:* string
  |
  | Path to php binary on current system.
  |


Changelog
---------

See https://github.com/sourcebroker/deployer-extended/blob/master/CHANGELOG.rst
