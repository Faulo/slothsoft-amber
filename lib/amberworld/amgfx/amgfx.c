/*
 _______          __ http://amberworld.sourceforge.net __    __
|   _   |--------.  |--.-----.----.--.--.--.-----.----.  |--|  |
|       |        |  _  |  -__|   _|  |  |  |  _  |   _|  |  _  |
|___|___|__|__|__|_____|_____|__| |________|_____|__| |__|_____|
         AMBERWORLD - THE AMBERMOON OPEN SOURCE PROJECT
*/
#include <ctype.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#if !defined(_WIN32) && !defined(WIN32) && !defined(AMIGA)
#include <strings.h>
#define strnicmp strncasecmp
#endif
#include "amgfx.h"
#include "palettes.h"

const char *version_string = "$VER: AMgfx 0.4 (23.03.2004)";

static int optidx(const char *str, int argc, char **argv);
static char *optarg(int idx, int argc, char **argv);
static int fsize(FILE *file);
static uchar getbit(uchar *bits, amb_u32 bitnr);

static char strOutfile[256];
static int numBpl = 5;
static int numPal = 49;
static int numWidth = 16;
static int numHeight = 0;
static int numOffset = 0;
static int numSize = 0;
static int firstColor = 0;
static uchar tmpPalette[3 * 64];

static void help(void)
{
	puts(&version_string[6]);
	puts("Ambermoon graphics converter by Oliver Gantert\n");
	puts("Usage: amgfx <filename> [options]");
	puts("Options:");
	puts("-out <filename>  specify output filename");
	puts("-b[pl] <num>     number of bitplanes (1-6) [5]");
	puts("-p[al] <num>     palette number (0-48) [0]");
	puts("-c[ol] <num>     first color in palette [0]");
	puts("-w[idth] <num>   image width (4-800) [16]");
	puts("-off <num>       offset into file [0]");
	puts("-s[ize] <num>    size in file");
}

#ifdef AMIGA
/* for compilers that don't have strnicmp (vbcc) */
static int strnicmp(const char *s1, const char *s2, size_t n)
{
  if (n == 0)
    return 0;
  do {
    if (tolower((unsigned char)*s1) != tolower((unsigned char)*s2++))
      return (int)tolower((unsigned char)*s1) - (int)tolower((unsigned char)*--s2);
    if (*s1++ == 0)
      break;
  } while (--n != 0);
  return 0;
}
#endif

static void convert(FILE *file)
{
	uchar *pixels, *ehb;
	uchar *line, col,b[6] = { 0,0,0,0,0,0 };
	int linew, bplw, x, y, p = 0;
	int numColors;

	uchar stat_col_hi = 0;
	uchar stat_col_lo = 255;

	bplw = numWidth >> 3;
	linew = bplw * numBpl;
	if (line = (uchar *)malloc(linew))
	{
		if (pixels = (uchar *)malloc(numWidth*numHeight))
		{
			for (y = 0; y < numHeight; y++)
			{
				fread(line, linew, 1, file);
				for (x = 0; x < numWidth; x++)
				{
					/* get color index from bitplanes */
					switch (numBpl)
					{
						case 6:
							b[5] = getbit(&line[bplw*5], x);
						case 5:
							b[4] = getbit(&line[bplw<<2], x);
						case 4:
							b[3] = getbit(&line[bplw*3], x);
						case 3:
							b[2] = getbit(&line[bplw<<1], x);
						case 2:
							b[1] = getbit(&line[bplw], x);
						case 1:
							b[0] = getbit(line, x);
						break;
					}

					col = (b[5]<<5)|(b[4]<<4)|(b[3]<<3)|(b[2]<<2)|(b[1]<<1)|b[0];

					col += firstColor;

					pixels[p++] = col;

					/* collect statistics */
					if (col > stat_col_hi) stat_col_hi = col;
					if (col < stat_col_lo) stat_col_lo = col;
				}
			}

			printf("Color index hi %d lo %d\n", stat_col_hi, stat_col_lo);

			memcpy(tmpPalette, &palettes[(numPal<<5)*3], 96);
			if (numBpl == 6)
			{
				/* create EHB (extra half-bright) palette */
				ehb = &tmpPalette[96];
				for (x = 0; x < 96; x++)
				{
					ehb[x] = tmpPalette[x] >> 1;
				}
			}

			if (firstColor) numColors = 32;
			else numColors = 1<<numBpl;

			if (SaveTGA8(strOutfile, pixels, numWidth, numHeight, tmpPalette, numColors))
				puts("Image saved");
			else
				puts("Failed to save image");
			free(pixels);
		}
		free(line);
	}
}

int main(int argc, char **argv)
{
	char *tmp;
	FILE *file;
	int filesize, BitsPerLine, BytesPerLine;

	if (argc > 1)
	{
		/* Open input */
		file = fopen(argv[1], "rb");
		if (!file)
		{
			puts("Failed to open input file");
			return(0);
		}

		/* Get filesize */
		filesize = fsize(file);
		if (!filesize)
		{
			puts("File is empty");
			goto label_close;
		}

		/* Get output filename */
		if (tmp = optarg(optidx("-out", argc, argv), argc, argv))
		{
			strcpy(strOutfile, tmp);
		}
		else
		{
			strcpy(strOutfile, argv[1]);
			strcat(strOutfile, ".tga");
		}

		/* Get number of bitplanes */
		if (tmp = optarg(optidx("-b", argc, argv), argc, argv))
		{
			numBpl = atoi(tmp);
			if (numBpl < 1 || numBpl > 6)
			{
				puts("Unsupported number of bitplanes (1-6)");
				goto label_close;
			}
		}

		/* Get palette number */
		if (tmp = optarg(optidx("-p", argc, argv), argc, argv))
		{
			numPal = atoi(tmp);
			if (numPal < 0 || numPal > 49)
			{
				puts("Unsupported palette number (0-49)");
				goto label_close;
			}
		}

		/* Get image width */
		if (tmp = optarg(optidx("-w", argc, argv), argc, argv))
		{
			numWidth = atoi(tmp);
			if (numWidth < 4 || numWidth > 800)
			{
				puts("Unsupported image width (4-800)");
				goto label_close;
			}
		}

		/* Custom filesize */
		if (tmp = optarg(optidx("-s", argc, argv), argc, argv))
		{
			numSize = atoi(tmp);
			if (numSize < 1 || numSize > filesize)
			{
				printf("Unsupported size (1-%d)\n", filesize);
				goto label_close;
			}
		}	/* else see below */

		/* Custom offset */
		if (tmp = optarg(optidx("-off", argc, argv), argc, argv))
		{
			numOffset = atoi(tmp);
			if (numOffset < 0 || numOffset+numSize > filesize)
			{
				puts("Offset out of range");
				goto label_close;
			}
		}

		if (tmp = optarg(optidx("-c", argc, argv), argc, argv))
		{
			firstColor = atoi(tmp);
		}

		/* if "-size" was not specified */
		if (!numSize) numSize = filesize - numOffset;

		if (numSize < 1)
		{	/* should be sorted out already... */
			puts("Fatal error! Please contact author");
			goto label_close;
		}

		/* check dimensions */
		BitsPerLine = numWidth * numBpl;
		BytesPerLine = BitsPerLine >> 3;
		if (BitsPerLine % 8)
		{
			BytesPerLine++;
			puts("Image width may be incorrect");
			/* continue anyway */
		}
		numHeight = numSize / BytesPerLine;
		if (numSize % BytesPerLine)
		{
			puts("Incorrect image dimensions");
			goto label_close;
		}

		/* set file offset */
		fseek(file, numOffset, SEEK_SET);

		convert(file);

label_close:
		if (file) fclose(file);
	}
	else help();
	return(0);
}

static int optidx(const char *str, int argc, char **argv)
{
	int i;
	for (i = 1; i < argc; i++)
	{
		if (!strnicmp(argv[i], str, strlen(str))) return(i);
	}
	return(0);
}

static char *optarg(int idx, int argc, char **argv)
{
	if (idx)
	{
		idx++;
		if (idx < argc) return(argv[idx]);
	}
	return(NULL);
}

/* get size of an open file */
static int fsize(FILE *file)
{
	int size;
	int curr = ftell(file);
	fseek(file, 0, SEEK_END);
	size = ftell(file);
	fseek(file, curr, SEEK_SET);
	return(size);
}

/* get bit in byte array */
static uchar getbit(uchar *bits, amb_u32 bitnr)
{
	amb_u32 bytenr = (bitnr >> 3);
	bitnr -= (bytenr << 3);
	bitnr = 7 - bitnr;
	return((bits[bytenr] & (1<<bitnr)) >> bitnr);
}
