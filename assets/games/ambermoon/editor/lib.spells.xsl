<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
	xmlns:sse="http://schema.slothsoft.net/savegame/editor">	
	
	<xsl:import href="globals/dictionary" />
	<xsl:import href="globals/savegame" />
	<xsl:import href="globals/editor" />
	<xsl:import href="globals/picker" />
	
	<xsl:template match="sse:archive[@name='AM2_CPU'] | sse:archive[@name='AM2_BLIT']" mode="form-content">
		<xsl:for-each select="sse:file">
			<xsl:variable name="spell-effects" select=".//*[@name = 'spell-effects']/*"/>
			<xsl:variable name="spell-data" select=".//*[@name = 'spell-data']/*"/>
			
			<xsl:call-template name="savegame.tabs">
				<xsl:with-param name="label" select="'Aktive Zauber-Kategorie:'" />
				<xsl:with-param name="options" select="saa:getDictionary('spell-types')/@val" />
				<xsl:with-param name="list">
					<xsl:for-each select="$spell-data">
						<xsl:variable name="spellbook-pos" select="position()"/>
						<xsl:variable name="list" select="*" />
						<div>
							<xsl:call-template name="savegame.tabs">
								<xsl:with-param name="label" select="'Aktiver Zauber:'" />
								<xsl:with-param name="options" select="saa:getDictionary(concat('spells-', position() - 1))/@val" />
								<xsl:with-param name="list">
									<xsl:for-each select="$list">
										<xsl:variable name="spell-pos" select="position()"/>
										<li>
											<xsl:call-template name="savegame.flex">
												<xsl:with-param name="items">
													<div>
														<xsl:call-template name="savegame.table">
															<xsl:with-param name="label" select="'basic data'" />
															<xsl:with-param name="items">
																<xsl:apply-templates select="*" mode="item" />
																<xsl:apply-templates select="$spell-effects[$spellbook-pos]/*[$spell-pos]/*" mode="item" />
															</xsl:with-param>
														</xsl:call-template>
													</div>
												</xsl:with-param>
											</xsl:call-template>
										</li>
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