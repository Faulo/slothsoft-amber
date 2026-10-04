/*
 _______          __ http://amberworld.sourceforge.net __    __
|   _   |--------.  |--.-----.----.--.--.--.-----.----.  |--|  |
|       |        |  _  |  -__|   _|  |  |  |  _  |   _|  |  _  |
|___|___|__|__|__|_____|_____|__| |________|_____|__| |__|_____|
         AMBERWORLD - THE AMBERMOON OPEN SOURCE PROJECT
*/
#include <stdlib.h>
#include <stdio.h>
#include "amblib.h"
#include "am_os.h"

const char *version_string = "$VER: AMBtool 0.6 (31.03.2004)";

static void save_file(const char *fname, void *mem, amb_u32 size)
{
	FILE *file;
	if (file = fopen(fname, "wb"))
	{
		fwrite(mem, size, 1, file);
		fclose(file);
	}
	else
	{
		puts("Failed to open output file.");
	}
}

int main(int argc, char **argv)
{
	AMB_Archive *pArchive;
	char szFile[256];
	amb_u32 numFiles, size;
	int i;
	void *mem;

	if (argc > 1)
	{
		pArchive = AMB_OpenArchive(argv[1]);
		if (!pArchive)
		{
			puts("Failed to open input file.");
			return(0);
		}

		// print some info
		printf("File: %s\nFormat: ", argv[1]);
		switch (AMB_GetArchiveType(pArchive))
		{
			case AMB_ID_JH:
				puts("JH (encrypted)");
			break;
			case AMB_ID_LOB:
			case AMB_ID_VOL1:
				puts("LOB (compressed)");
			break;
			case AMB_ID_AMNC:
				puts("AMNC (encrypted archive)");
			break;
			case AMB_ID_AMNP:
				puts("AMNP (compressed/encrypted archive)");
			break;
			case AMB_ID_AMBR:
				puts("AMBR (raw archive)");
			break;
			case AMB_ID_AMPC:
				puts("AMPC (compressed archive)");
			break;
		}

		numFiles = AMB_GetNumFiles(pArchive);
		printf("# Entries: %u\n", numFiles);

		if (argc > 2)
		{
			if (makedir(argv[2]))
			{
				while (i = AMB_GetNextFile(pArchive))
				{
					size = AMB_GetFileSize(pArchive);
					if (!size) continue;

					printf("%u\n", size);

					if (mem = malloc(size))
					{
						if (AMB_ReadFile(pArchive, mem))
						{
							if (snprintf(szFile, sizeof(szFile), "%s%c%03d", argv[2], DIRSLASH, i) >= (int)sizeof(szFile)) return(1);
							save_file(szFile, mem, size);
						}
						free(mem);
					}
				}
			}
		}

		AMB_CloseArchive(pArchive);
	}
	else
	{
		puts(&version_string[6]);
		puts("AMB file decoder by Oliver Gantert\n");
		puts("Usage: ambtool <filename> [<output>]\n");
	}

	return(0);
}
