/*
 _______          __ http://amberworld.sourceforge.net __    __
|   _   |--------.  |--.-----.----.--.--.--.-----.----.  |--|  |
|       |        |  _  |  -__|   _|  |  |  |  _  |   _|  |  _  |
|___|___|__|__|__|_____|_____|__| |________|_____|__| |__|_____|
         AMBERWORLD - THE AMBERMOON OPEN SOURCE PROJECT
*/
#ifndef __am_os_h__
#define __am_os_h__

#include "amblib.h"

#if defined(_WIN32) || defined(WIN32)
#define DIRSLASH	'\\'
#else
#define DIRSLASH	'/'
#endif

extern int makedir(const char *path);

#endif /* __am_os_h__ */
