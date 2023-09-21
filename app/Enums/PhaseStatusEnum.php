<?php

namespace App\Enums;

enum PhaseStatusEnum: string
{
	case enAttente = 'en_attente';
	case enCours = 'en_cours';
	case Terminé = 'termine';
}
