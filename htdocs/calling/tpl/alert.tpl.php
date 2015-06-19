<?php


			/**
					Block display info for collaborateur called
			*/
			if( $conf->global->CALLING_ALERT_TYPE_MODE_DISPLAY_BLOCK_USER == 1 && isset($res->collaborateur) &&  $res->collaborateur != $user->id ) {
// 					$collegue = new User($db);
// 					$collegue->fetch( $res->collaborateur);
						echo '<h3 style="font-size:14px; margin:0; padding:0;">'.$langs->trans("CallingAlertTiersCollaborateur").' - '. $res->collaborateur->name.' '. $res->collaborateur->lastname .'</h3>
									<ul>
										<li><a href="http://'.$_SERVER['HTTP_HOST'].DOL_URL_ROOT.'/user/card.php?id='. $res->collaborateur->id .'">'.$langs->trans("CallingAlertUserFiche").'</a></li>
									</ul><hr />';
			}

			/**
					Block display info for incoming calling
			*/
			if(isset($res->user) && is_array($res->user) )
				foreach($res->user as $row){

					switch($row->type){
						case 'contact':
							echo '<h3 style="font-size:14px; margin:0; padding:0;">'.$langs->trans("CallingAlertTiersContact").' - '. $row->name.'</h3>
										<ul>
											<li><a href="http://'.$_SERVER['HTTP_HOST'].DOL_URL_ROOT.'/contact/card.php?id='. $row->id .'">'.$langs->trans("CallingAlertFiche").'</a></li>
											<li><a href="http://'.$_SERVER['HTTP_HOST'].DOL_URL_ROOT.'/contact/card.php?id='. $row->id .'&amp;action=edit">'.$langs->trans("CallingAlertEdit").'</a></li>
										</ul>';
						break;
						case 'societe':
							echo '<h2 style="font-size:14px; margin:0; padding:0;">'.$langs->trans("CallingAlertTiersSociete").'</h2> '.
										'<br />'.
										'<a href="http://'.$_SERVER['HTTP_HOST'].DOL_URL_ROOT.'/societe/soc.php?socid='. $row->id .'">'. $row->name.'</a>';
							echo ''.//'<h3 style="font-size:14px; margin:0; padding:0;">'.$langs->trans("CallingAlertTiersContact").' - '. $row->name.'</h3>
										'<ul>
											<li><a href="http://'.$_SERVER['HTTP_HOST'].DOL_URL_ROOT.'/societe/soc.php?socid='. $row->id .'">'.$langs->trans("CallingAlertFiche").'</a></li>
											<li><a href="http://'.$_SERVER['HTTP_HOST'].DOL_URL_ROOT.'/societe/soc.php?socid='. $row->id .'&amp;action=edit">'.$langs->trans("CallingAlertEdit").'</a></li>
											<li><a href="http://'.$_SERVER['HTTP_HOST'].DOL_URL_ROOT.'/societe/soc.php?socid='. $row->id .'&amp;action=edit&status=todo">'.$langs->trans("CallingAlertTodo").'</a></li>
										</ul>';
						break;
					}
				}


?>
