###Install

This needs a makefile to be installed properly

Create a symlink somewhere in your $PATH:

`# ln -s /path/coming_bus.php /usr/bin/bus`

and now you can 

`$ bus -b 136 -s 73346`

using the stop and bus you want.

You can even store your favourite bus and stop in ~/.bus
You have an example configuration file in _.bus

usage: 
`bus [-s stop_number] [-b bus1[:bus2]]`

~/.bus JSON format: 
`{"buses":[123],"stop":12345}`
