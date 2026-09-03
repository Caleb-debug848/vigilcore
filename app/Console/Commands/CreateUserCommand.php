<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create 
                            {email? : Adresse e-mail du collaborateur}
                            {--name= : Nom complet de l\'agent}
                            {--password= : Mot de passe initial}
                            {--send-link : Envoyer automatiquement le lien de réinitialisation 15 min}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crée un nouvel utilisateur VigilCore de manière sécurisée et interactive';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("🛡️  VIGILCORE — CRÉATION SÉCURISÉE D'UN COMPTE COLLABORATEUR");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        // 1. Nom
        $name = $this->option('name');
        if (!$name) {
            $name = $this->ask('👤 Nom complet de l\'agent (ex: Eva Danielle Njampa)');
            while (empty(trim($name))) {
                $this->error('Le nom complet est obligatoire.');
                $name = $this->ask('👤 Nom complet de l\'agent');
            }
        }

        // 2. Email
        $email = $this->argument('email');
        if (!$email) {
            $email = $this->ask('📧 Adresse e-mail (ex: eva.njampa@maviance.cm ou gmail)');
            while (empty(trim($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->error('Veuillez saisir une adresse e-mail valide.');
                $email = $this->ask('📧 Adresse e-mail');
            }
        }

        $email = strtolower(trim($email));

        // Vérification si le compte existe déjà
        $existing = User::where('email', $email)->first();
        if ($existing) {
            $this->warn("⚠️ Un compte existe déjà avec l'e-mail [$email] (Nom: {$existing->name}).");
            if (!$this->confirm('Voulez-vous modifier les informations de cet utilisateur ?', true)) {
                $this->info('Opération annulée.');
                return Command::SUCCESS;
            }
        }

        // 3. Choix du mot de passe
        $password = $this->option('password');
        $sendLink = $this->option('send-link');

        if (!$password && !$sendLink) {
            $this->newLine();
            $choice = $this->choice(
                '🔑 Comment voulez-vous définir le mot de passe initial ?',
                [
                    '1' => 'Définir moi-même un mot de passe maintenant',
                    '2' => 'Envoyer automatiquement un lien sécurisé (15 min) par e-mail',
                ],
                '1'
            );

            if ($choice === '1' || str_contains($choice, 'Définir')) {
                $password = $this->secret('Saisissez le mot de passe (min. 8 caractères) :');
                while (strlen($password) < 8) {
                    $this->error('Le mot de passe doit comporter au moins 8 caractères.');
                    $password = $this->secret('Saisissez le mot de passe (min. 8 caractères) :');
                }
            } else {
                $sendLink = true;
                // Mot de passe temporaire aléatoire fort
                $password = Str::random(32);
            }
        } elseif (!$password) {
            $password = Str::random(32);
        }

        // Création / Mise à jour de l'utilisateur
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'     => $name,
                'password' => Hash::make($password),
            ]
        );

        $this->newLine();
        $this->info("✅ COMPTE CRÉÉ AVEC SUCCÈS !");
        $this->table(
            ['ID', 'Nom', 'Email', 'Mode Mot de Passe'],
            [
                [$user->id, $user->name, $user->email, $sendLink ? 'Lien e-mail (15 min)' : 'Défini manuellement']
            ]
        );

        // Envoi du lien si demandé
        if ($sendLink) {
            $this->info("📤 Envoi de l'e-mail d'invitation sécurisé à [$email]...");
            try {
                $status = Password::sendResetLink(['email' => $email]);
                if ($status === Password::RESET_LINK_SENT) {
                    $this->info("✓ E-mail officiel expédié avec succès ! L'utilisateur dispose de 15 minutes pour définir son mot de passe.");
                } else {
                    $this->warn("Notification : " . __($status));
                }
            } catch (\Throwable $e) {
                $this->error("Erreur lors de l'envoi SMTP : " . $e->getMessage());
                $this->comment("L'utilisateur est tout de même créé. Vous pouvez lui définir un mot de passe avec : php artisan user:password $email");
            }
        }

        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        return Command::SUCCESS;
    }
}
