<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
	xmlns:sse="http://schema.slothsoft.net/savegame/editor">

	<xsl:import href="globals/dictionary" />
	<xsl:import href="globals/savegame" />
	<xsl:import href="globals/editor" />
	<xsl:import href="globals/picker" />
	<xsl:import href="globals/maps" />

	<xsl:template match="sse:archive[@name='2D_Map_data.amb']" mode="form-content">
		<xsl:call-template name="maps.data-file" />
	</xsl:template>
	<xsl:template match="sse:archive[@name='2D_Icon_data.amb']" mode="form-content">
		<xsl:call-template name="maps.tileset-file" />
	</xsl:template>

	<xsl:template match="sse:archive[@name='3D_Map_data.amb']" mode="form-content">
		<xsl:call-template name="maps.data-file" />
	</xsl:template>
	<xsl:template match="sse:archive[@name='3D_Lab_data.amb']" mode="form-content">
		<xsl:call-template name="maps.tileset-file" />
	</xsl:template>

	<xsl:template match="sse:archive[@name='Abstract_data.amb']" mode="form-content">
		<xsl:for-each select="sse:file">
			<xsl:call-template name="savegame.flex">
				<xsl:with-param name="items">
					<xsl:for-each select="*[@name = 'classes']">
						<div>
							<xsl:call-template name="savegame.flex">
								<xsl:with-param name="label" select="'Klassen'" />
								<xsl:with-param name="items">
									<xsl:for-each select="*/*">
										<div>
											<xsl:apply-templates select=".//*[@name = 'name']" mode="form-content" />
											<xsl:call-template name="savegame.amber.character-class" />
										</div>
									</xsl:for-each>
								</xsl:with-param>
							</xsl:call-template>
						</div>
					</xsl:for-each>
				</xsl:with-param>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>