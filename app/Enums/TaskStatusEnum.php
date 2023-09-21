<?php

namespace App\Enums;

enum TaskStatusEnum: string
{
	case enCours = 'en_cours';
	case enAttente = 'en_attente';
	case nonAboutti = 'non_aboutti';
	case Terminé = 'termine';
	case Expiré = 'expire';
}