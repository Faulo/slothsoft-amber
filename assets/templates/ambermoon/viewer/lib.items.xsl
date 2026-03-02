<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata">
	<xsl:import href="farah://slothsoft@amber/templates/ambermoon/viewer/globals/amber-list" />

	<xsl:template match="saa:item-list">
		<xsl:call-template name="amber-list" />
	</xsl:template>

	<xsl:template match="saa:item-category">
		<xsl:call-template name="amber-category" />
	</xsl:template>

	<xsl:template match="saa:item">
		<!--<item xmlns="" id="361" image="9" name="MAGIERSTIEFEL" type="Schuhe" hands="0" fingers="0" damage="0" armor="6" weight="850" gender="beide" class="Magier Mystik. Alchem. Heiler"/> -->
		<article data-item-id="{@id}" class="amber-item amber-text">
			<div>
				<div>
					<div class="amber-item__header">
						<div class="amber-item__gfx">
							<amber-embed infoset="lib.items" type="item-gfx">
								<amber-item-gfx value="{@image-id}" />
							</amber-embed>
						</div>
						<div class="amber-item__name">
							<div class="amber-text amber-text--yellow">
								<xsl:value-of select="@name" />
							</div>
							<div>
								<xsl:value-of select="@type" />
							</div>
						</div>
					</div>
				</div>
				<table>
					<tbody class="amber-item__worth">
						<tr>
							<td>Gewicht:</td>
							<td>
								<div class="amber-item__value">
									<xsl:value-of select="concat(@weight, ' gr')" />
								</div>
							</td>
						</tr>
						<tr>
							<td>Wert:</td>
							<td>
								<div class="amber-item__value">
									<xsl:value-of select="concat(@price, ' gp')" />
								</div>
							</td>
						</tr>
					</tbody>
					<tbody class="amber-item__stats">
						<tr>
							<td>Hände:</td>
							<td>
								<div class="amber-item__value">
									<xsl:value-of select="@hands" />
								</div>
							</td>
						</tr>
						<tr>
							<td>Finger:</td>
							<td>
								<div class="amber-item__value">
									<xsl:value-of select="@fingers" />
								</div>
							</td>
						</tr>
						<tr>
							<td>Schaden:</td>
							<td>
								<div class="amber-item__value">
									<xsl:if test="@damage &gt; 0">
										<xsl:text>+</xsl:text>
									</xsl:if>
									<xsl:value-of select="@damage" />
								</div>
							</td>
						</tr>
						<tr>
							<td>Schutz:</td>
							<td>
								<div class="amber-item__value">
									<xsl:if test="@armor &gt; 0">
										<xsl:text>+</xsl:text>
									</xsl:if>
									<xsl:value-of select="@armor" />
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<xsl:choose>
				<xsl:when test="saa:text">
					<div class="amber-textbox">
						<xsl:for-each select="saa:text/saa:paragraph">
							<p>
								<xsl:attribute name="class">
                                    <xsl:text>amber-text</xsl:text>
                                    <xsl:choose>
                                        <xsl:when test="@ink">
                                            <xsl:value-of select="concat(' amber-text--', @ink)" />
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <xsl:text> amber-text--silver</xsl:text>
                                        </xsl:otherwise>
                                    </xsl:choose>
                                    <xsl:if test="@language">
                                        <xsl:value-of select="concat(' amber-text--', @language)" />
                                    </xsl:if>
                                </xsl:attribute>
								<xsl:value-of select="normalize-space(.)" />
							</p>
						</xsl:for-each>
					</div>
				</xsl:when>
				<xsl:otherwise>
					<dl class="amber-item__restrictions">
						<dt class="amber-text amber-text--gray">------ Klassen ------</dt>
						<dd class="amber-item__classes">
							<ul>
								<xsl:for-each select="saa:class-reference/@name">
									<li>
										<xsl:value-of select="." />
									</li>
								</xsl:for-each>
							</ul>
						</dd>
						<dt class="amber-text amber-text--gray">Geschlecht</dt>
						<dt class="amber-item__properties amber-text amber-text--gray">Merkmale</dt>
						<dd>
							<xsl:value-of select="@gender" />
						</dd>
						<dd class="amber-item__properties">
							<xsl:choose>
								<xsl:when test="@is-disposable">
									<span class="amber-icon amber-icon--is-important amber-icon--not" data-hover-text="wegwerfbar" />
								</xsl:when>
								<xsl:otherwise>
									<span class="amber-icon amber-icon--is-important" data-hover-text="wichtig" />
								</xsl:otherwise>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="@is-stackable">
									<span class="amber-icon amber-icon--is-stackable" data-hover-text="stapelbar" />
								</xsl:when>
								<xsl:otherwise>
									<span class="amber-icon amber-icon--is-stackable amber-icon--not" data-hover-text="nicht stapelbar" />
								</xsl:otherwise>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="@is-combat-equippable">
									<span class="amber-icon amber-icon--is-combat-equippable" data-hover-text="im kampf ausrüstbar" />
								</xsl:when>
								<xsl:otherwise>
									<span class="amber-icon amber-icon--is-combat-equippable amber-icon--not" data-hover-text="nicht im kampf ausrüstbar" />
								</xsl:otherwise>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="@is-useable">
									<span class="amber-icon amber-icon--is-useable" data-hover-text="verbraucht durch benutzung" />
								</xsl:when>
								<xsl:otherwise>
									<span class="amber-icon amber-icon--is-useable amber-icon--not" data-hover-text="benutzung verbraucht nicht" />
								</xsl:otherwise>
							</xsl:choose>
							<xsl:choose>
								<xsl:when test="@is-cloneable">
									<span class="amber-icon amber-icon--is-cloneable" data-hover-text="duplizierbar" />
								</xsl:when>
								<xsl:otherwise>
									<span class="amber-icon amber-icon--is-cloneable amber-icon--not" data-hover-text="nicht duplizierbar" />
								</xsl:otherwise>
							</xsl:choose>
						</dd>
					</dl>
					<table class="amber-item__magic">
						<tbody>
							<tr>
								<td title="Lebenspunkte Maximum">LP-Max: </td>
								<td class="amber-item__value">
									<xsl:if test="@lp-max &gt; 0">
										<xsl:choose>
											<xsl:when test="@is-cursed">
												<xsl:text>-</xsl:text>
											</xsl:when>
											<xsl:otherwise>
												<xsl:text>+</xsl:text>
											</xsl:otherwise>
										</xsl:choose>
									</xsl:if>
									<xsl:value-of select="@lp-max" />
								</td>
								<td title="Spruchpunkte Maximum">SP-Max: </td>
								<td class="amber-item__value">
									<xsl:if test="@sp-max &gt; 0">
										<xsl:choose>
											<xsl:when test="@is-cursed">
												<xsl:text>-</xsl:text>
											</xsl:when>
											<xsl:otherwise>
												<xsl:text>+</xsl:text>
											</xsl:otherwise>
										</xsl:choose>
									</xsl:if>
									<xsl:value-of select="@sp-max" />
								</td>
							</tr>
							<tr>
								<td title="Magischer Rüstschutz, Angriff">M-B-W: </td>
								<td class="amber-item__value">
									<xsl:if test="@magic-weapon &gt; 0">
										<xsl:choose>
											<xsl:when test="@is-cursed">
												<xsl:text>-</xsl:text>
											</xsl:when>
											<xsl:otherwise>
												<xsl:text>+</xsl:text>
											</xsl:otherwise>
										</xsl:choose>
									</xsl:if>
									<xsl:value-of select="@magic-weapon" />
								</td>
								<td title="Magischer Rüstschutz, Verteidigung">M-B-R: </td>
								<td class="amber-item__value">
									<xsl:if test="@magic-armor &gt; 0">
										<xsl:choose>
											<xsl:when test="@is-cursed">
												<xsl:text>-</xsl:text>
											</xsl:when>
											<xsl:otherwise>
												<xsl:text>+</xsl:text>
											</xsl:otherwise>
										</xsl:choose>
									</xsl:if>
									<xsl:value-of select="@magic-armor" />
								</td>
							</tr>
						</tbody>
						<tbody>
							<tr>
								<td colspan="4" class="amber-text amber-text--orange">
									<xsl:if test="@attribute-value &gt; 0">
										Attribut
									</xsl:if>
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<xsl:if test="@attribute-value &gt; 0">
										<xsl:value-of select="@attribute-type" />
									</xsl:if>
								</td>
								<td class="amber-item__value">
									<xsl:if test="@attribute-value &gt; 0">
										<xsl:choose>
											<xsl:when test="@is-cursed">
												<xsl:text>-</xsl:text>
											</xsl:when>
											<xsl:otherwise>
												<xsl:text>+</xsl:text>
											</xsl:otherwise>
										</xsl:choose>
										<xsl:value-of select="@attribute-value" />
									</xsl:if>
								</td>
							</tr>
						</tbody>
						<tbody>
							<tr>
								<td colspan="4" class="amber-text amber-text--orange">
									<xsl:if test="@skill-value &gt; 0 or @hidden-skill-value &gt; 0">
										Fähigkeit
									</xsl:if>
								</td>
							</tr>
							<tr>
								<td colspan="3">
									<xsl:if test="@skill-value &gt; 0">
										<xsl:value-of select="@skill-type" />
									</xsl:if>
									<xsl:if test="@hidden-skill-value &gt; 0">
										<xsl:value-of select="@hidden-skill-type" />
									</xsl:if>
								</td>
								<td class="amber-item__value">
									<xsl:if test="@skill-value &gt; 0">
										<xsl:choose>
											<xsl:when test="@is-cursed">
												<xsl:text>-</xsl:text>
											</xsl:when>
											<xsl:otherwise>
												<xsl:text>+</xsl:text>
											</xsl:otherwise>
										</xsl:choose>
										<xsl:value-of select="@skill-value" />
									</xsl:if>
									<xsl:if test="@hidden-skill-value &gt; 0">
										<xsl:text>-</xsl:text>
										<xsl:value-of select="@hidden-skill-value" />
									</xsl:if>
								</td>
							</tr>
						</tbody>
						<tbody>
							<tr>
								<td colspan="4" class="amber-text amber-text--orange">
									<xsl:if test="@spell-id &gt; 0">
										<xsl:value-of select="@spell-type" />
									</xsl:if>
								</td>
							</tr>
							<tr>
								<td colspan="4">
									<xsl:if test="@spell-id &gt; 0">
										<xsl:value-of select="concat(@spell-name, ' (', @charges-default, ')')" />
									</xsl:if>
									<xsl:if test="@is-cursed">
										<div class="amber-item__cursed amber-text amber-text--red">verflucht!</div>
									</xsl:if>
								</td>
							</tr>
						</tbody>
					</table>
				</xsl:otherwise>
			</xsl:choose>
		</article>
	</xsl:template>
</xsl:stylesheet>
