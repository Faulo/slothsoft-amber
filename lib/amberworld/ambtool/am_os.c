/*
 _______          __ http://amberworld.sourceforge.net __    __
|   _   |--------.  |--.-----.----.--.--.--.-----.----.  |--|  |
|       |        |  _  |  -__|   _|  |  |  |  _  |   _|  |  _  |
|___|___|__|__|__|_____|_____|__| |________|_____|__| |__|_____|
         AMBERWORLD - THE AMBERMOON OPEN SOURCE PROJECT
*/
#include "am_os.h"

#if defined(_WIN32) || defined(WIN32)
#include <windows.h>
#elif defined(AMIGA)
#include <exec/types.h>
#include <dos/dos.h>
#include <proto/dos.h>
#else
#include <errno.h>
#include <sys/stat.h>
#endif

/* create directory, return 0 on error */
int makedir(const char *path)
{
#if defined(_WIN32) || defined(WIN32)
	BOOL bSuccess = CreateDirectory(path, NULL);
	if ((!bSuccess) && (GetLastError() != ERROR_ALREADY_EXISTS))
	{
		return(0);
	}
	return(~0);
#elif defined(AMIGA)
	BPTR pLock;
	if (pLock = CreateDir(path))
	{
		UnLock(pLock);
		return(~0);
	}
	return(0);
#else
	return mkdir(path, 0777) == 0 || errno == EEXIST;
#endif
}
