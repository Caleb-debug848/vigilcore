<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ManageAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vigilcore:admin 
                            {email? : L\'adresse email de l\'administrateur} 
                            {password? : Le nouveau mot de passe} 
                            {--name= : Le nom de l\'utilisateur}
                            {--list : Lister tous les comptes administrateurs existants}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gère, crée ou réinitialise le mot de passe d\'un compte administrateur VigilCore en 1 seconde';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Option 1 : Lister les utilisateurs
        if ($this->option('list')) {
            $users = User::select('id', 'name', 'email', 'created_at')->get();
            if ($users->isEmpty()) {
                $this->warn('Aucun utilisateur trouvé en base de données.');
                return Command::SUCCESS;
            }

            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("📋 COMPTES UTILISATEURS VIGILCORE :");
            $this->table(['ID', 'Nom', 'Email', 'Créé le'], $users->toArray());
            $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            return Command::SUCCESS;
        }

        $email = $this->argument('email') ?? 'calebdassi@gmail.com';
        $password = $this->argument('password') ?? 'password123';
        $name = $this->option('name') ?? 'Caleb Dassi';

        // Recherche ou création de l'utilisateur
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'     => $name,
                'password' => Hash::make($password),
            ]
        );

        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("✅ COMPTE ADMINISTRATEUR CONFIGURÉ AVEC SUCCÈS !");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->table(['Paramètre', 'Valeur'], [
            ['Nom', $user->name],
            ['Email', $user->email],
            ['Mot de passe', $password],
            ['Statut', $user->wasRecentlyCreated ? 'Nouveau compte créé' : 'Mot de passe mis à jour'],
        ]);
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->comment("👉 Vous pouvez désormais vous connecter immédiatement sur :");
        $this->line("   https://vigilcore.calebdevs.com/login");
        $this->line("   ou en local sur : http://127.0.0.1:8000/login");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        return Command::SUCCESS;
    }
}
