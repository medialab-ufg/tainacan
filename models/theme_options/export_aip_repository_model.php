<?php
/**
 * Model que realiza a exportacao do zip AIP do tainacan
 */
class ExportAIPRepositoryModel extends ExportAIPModel {
    
    public $XML;
    
    /**
     * metodo que executa os demais para criacao do mets e do zip do repositorio
     */
    public function create_repository() {
        $dir_repo = $this->dir.'/'.$this->name_folder.'/'.$this->name_folder_repository;
        $this->recursiveRemoveDirectory($dir_repo);
        if(!is_dir($dir_repo.'/')){
             mkdir($dir_repo);
        }
        $this->generate_xml();
        $this->create_xml_file($dir_repo.'/mets.xml', $this->XML);
        $this->create_zip_by_folder($this->dir.'/'.$this->name_folder.'/', $this->name_folder_repository.'/', $this->name_folder_repository,true);
        $this->recursiveRemoveDirectory($dir_repo);
    }
    
    
    public function generate_xml(){
        $this->XML = '<?xml version="1.0" encoding="utf-8" standalone="no"?>';
        $this->XML .= '<mets ID="DSpace_SITE_ri-0" OBJID="hdl:ri/0" TYPE="DSpace SITE" '
                . 'PROFILE="http://www.dspace.org/schema/aip/mets_aip_1_0.xsd" '
                . 'xmlns="http://www.loc.gov/METS/" '
                . 'xmlns:xlink="http://www.w3.org/1999/xlink" '
                . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                . 'xsi:schemaLocation="http://www.loc.gov/METS/ http://www.loc.gov/standards/mets/mets.xsd">';
        $this->XML .= trim('<metsHdr>
                            <agent ROLE="CUSTODIAN" TYPE="OTHER" OTHERTYPE="DSpace Archive">
                                <name>ri/0</name>
                            </agent>
                            <agent ROLE="CREATOR" TYPE="OTHER" OTHERTYPE="DSpace Software">
                                <name>DSpace 5.5</name>
                            </agent>
                       </metsHdr>');
        $this->XML .= '<dmdSec ID="dmdSec_1">
                        <mdWrap MDTYPE="MODS">
                         <xmlData xmlns:mods="http://www.loc.gov/mods/v3" 
                         xmlns:xlink="http://www.w3.org/1999/xlink" 
                         xsi:schemaLocation="http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-1.xsd">
                         <mods:mods xmlns:mods="http://www.loc.gov/mods/v3" 
                         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                         xsi:schemaLocation="http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-1.xsd">
                        <mods:identifier type="uri">'.get_bloginfo('url').'</mods:identifier>
                        <mods:titleInfo>
                          <mods:title>'.get_bloginfo('name').'</mods:title>
                        </mods:titleInfo>
                      </mods:mods></xmlData>
                        </mdWrap>
                       </dmdSec>
                      ';
        $this->XML .= '<dmdSec ID="dmdSec_2">
                            <mdWrap MDTYPE="OTHER" OTHERMDTYPE="DIM">
                             <xmlData xmlns:dim="http://www.dspace.org/xmlns/dspace/dim"><dim:dim xmlns:dim="http://www.dspace.org/xmlns/dspace/dim" dspaceType="SITE">
                                <dim:field mdschema="dc" element="identifier" qualifier="uri">hdl:ri/0</dim:field>
                                <dim:field mdschema="dc" element="identifier" qualifier="uri">'.get_bloginfo('url').'</dim:field>
                                <dim:field mdschema="dc" element="title">'.get_bloginfo('name').'</dim:field>
                                </dim:dim>
                              </xmlData>
                            </mdWrap>
                        </dmdSec>
                      ';
        $this->generate_xml_groups_and_users();
        $this->generate_community_xml();
        $this->XML .= '</mets>';
    }
    
    /**
     *  gera o xml dos grupos do tainacan
     */
    public function generate_xml_groups_and_users(){
        $this->XML .= '
            <amdSec ID="amd_3">
                <techMD ID="techMD_5">
                 <mdWrap MDTYPE="OTHER" OTHERMDTYPE="DSPACE-ROLES">
                  <xmlData xmlns:dsroles="http://www.dspace.org/xmlns/dspace/dspace-roles">
                  <DSpaceRoles>
                    <Groups>'; 
        $this->XML .= '<Group ID="0" Name="Anonymous" />';
        $this->XML .= '<Group ID="1" Name="Administrator">'.$this->get_users_by_group('admin').'</Group>';
        $this->XML .= '<Group ID="2" Name="Members">'.$this->get_users_by_group('members').'</Group>';
        $this->XML .= '</Groups>';
        $this->generate_xml_users();
        $this->XML .= '
                   </DSpaceRoles>
                   </xmlData>
                </mdWrap>
              </techMD>
                <sourceMD ID="sourceMD_10">
                 <mdWrap MDTYPE="OTHER" OTHERMDTYPE="AIP-TECHMD">
                    <xmlData xmlns:dim="http://www.dspace.org/xmlns/dspace/dim"><dim:dim xmlns:dim="http://www.dspace.org/xmlns/dspace/dim" dspaceType="SITE">
                        <dim:field mdschema="dc" element="identifier" qualifier="uri">hdl:ri/0</dim:field>
                        <dim:field mdschema="dc" element="identifier" qualifier="uri">'.get_bloginfo('url').'</dim:field>
                      </dim:dim>
                    </xmlData>
                 </mdWrap>
                </sourceMD>
            </amdSec>'; 
    }
    
    /**
     * busca no banco os usuario para cada role
     */
    public function get_users_by_group($group) {
        $valor = '';
        if($group=='admin'){
            $blogusers = get_users( 'role=administrator' );
        }else if($group=='members'){
            $blogusers = get_users( 'role=subscriber' );
        }
        if($blogusers){
            $valor .= '<Members>';
            foreach ( $blogusers as $user ) {
                    $this->XML .= '<Member ID="'.$user->ID.'" Name="' . esc_html( $user->user_email ) . '" />';
            }
            $valor .= '</Members>';
        }
        return $valor;
    }
    
    /**
     * metodo que cria o xml de todos os usuarios
     */
    public function generate_xml_users(){
        $users = get_users();
         if($users){
            $this->XML .= '<People>';
            foreach ( $users as $user ) {
                    $this->XML .= '<Person ID="'.$user->ID.'" >';
                    $this->XML .= '<Email>' . esc_html( $user->user_email ) . '</Email>';
                    $this->XML .= '<FirstName>' . esc_html( $user->user_firstname ) . '</FirstName>';
                    $this->XML .= '<LastName>' . esc_html( $user->user_lasttname ) . '</LastName>';
                    $this->XML .= '<Language>' . get_locale() . '"</Language>';
                    $this->XML .= '<CanLogin /><SelfRegistered />';
                    $this->XML .= '</Person>';
            }
            $this->XML .= '</People>';
        }
    }
    
    /**
     * metodo que cria a estrutura das comunidades
     */
    public function generate_community_xml() {
        $this->XML .= '<structMap ID="struct_11" LABEL="DSpace Object" TYPE="LOGICAL">';
        $this->XML .= '<div ID="div_12" DMDID="dmdSec_2 dmdSec_1" ADMID="amd_3" TYPE="DSpace Object Contents">';
        $this->XML .= '<div ID="div_13" TYPE="DSpace COMMUNITY">';
        $this->XML .= '<mptr ID="mptr_14" LOCTYPE="HANDLE" xlink:type="simple" xlink:href="'.$this->prefix.'/'. get_option('collection_root_id').'"/>';
        $this->XML .= '<mptr ID="mptr_15" LOCTYPE="URL" xlink:type="simple" xlink:href="COMMUNITY@'.$this->prefix.'-'. get_option('collection_root_id').'.zip"/>';
        $this->XML .= '</div>';
        $this->XML .= '</div>';
        $this->XML .= '</structMap>';
    }
    
}
