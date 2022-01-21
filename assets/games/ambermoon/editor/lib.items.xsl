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
			<xsl:for-each select="*[@name = 'items']">
				<xsl:variable name="categories" select="key('dictionary-option', 'item-types')" />
				<xsl:variable name="items" select="*" />
				<xsl:call-template name="savegame.tabs">
					<xsl:with-param name="label" select="'Aktive Item-Kategorie:'" />
					<xsl:with-param name="options" select="$categories/@val" />
					<xsl:with-param name="list">
						<xsl:for-each select="$categories">
							<xsl:variable name="list" select="$items[.//*[@name = 'type']/@value = current()/@key]" />
							<li>
								<xsl:call-template name="savegame.tabs">
									<xsl:with-param name="label" select="'Aktives Item:'" />
									<xsl:with-param name="options" select="$list//*[@name = 'name']/@value" />
									<xsl:with-param name="list">
										<xsl:for-each select="$list">
											<li>
												<xsl:call-template name="savegame.flex">
													<xsl:with-param name="items">
														<div>
															<xsl:call-template name="savegame.table">
																<xsl:with-param name="label" select="'basic data'" />
																<xsl:with-param name="items">
																	<xsl:apply-templates select=".//*[@name = 'name']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'type']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'image-id']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'price']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'weight']" mode="item" />
																</xsl:with-param>
															</xsl:call-template>
															<xsl:apply-templates select=".//*[@name = 'classes']" mode="item" />
															<xsl:apply-templates select=".//*[@name = 'default-status']" mode="item" />
														</div>
														<div>
															<xsl:call-template name="savegame.table">
																<xsl:with-param name="label" select="'equipment'" />
																<xsl:with-param name="items">
																	<xsl:apply-templates select=".//*[@name = 'slot']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'hands']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'fingers']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'ranged-type']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'ammunition-type']" mode="item" />
																</xsl:with-param>
															</xsl:call-template>
															<xsl:call-template name="savegame.table">
																<xsl:with-param name="label" select="'stats'" />
																<xsl:with-param name="items">
																	<xsl:apply-templates select=".//*[@name = 'damage']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'armor']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'magic-weapon']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'magic-armor']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'lp-max']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'sp-max']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'attribute-type']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'attribute-value']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'skill-type']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'skill-value']" mode="item" />
																</xsl:with-param>
															</xsl:call-template>
														</div>
														<div>
															<xsl:call-template name="savegame.table">
																<xsl:with-param name="label" select="'enchantment'" />
																<xsl:with-param name="items">
																	<xsl:apply-templates select=".//*[@name = 'spell-type']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'spell-id']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'charges-default']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'max-charges-by-spell']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'max-charges-by-shop']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'price-per-charge']" mode="item" />
																</xsl:with-param>
															</xsl:call-template>
															<xsl:call-template name="savegame.table">
																<xsl:with-param name="label" select="'special'" />
																<xsl:with-param name="items">
																	<xsl:apply-templates select=".//*[@name = 'subtype']" mode="item" />
																	<xsl:apply-templates select=".//*[@name = 'subsubtype']" mode="item" />
																</xsl:with-param>
															</xsl:call-template>
															<xsl:apply-templates select=".//*[@name = 'properties']" mode="item" />
														</div>
													</xsl:with-param>
												</xsl:call-template>
											</li>
										</xsl:for-each>
									</xsl:with-param>
								</xsl:call-template>
							</li>
						</xsl:for-each>
					</xsl:with-param>
				</xsl:call-template>
			</xsl:for-each>
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>