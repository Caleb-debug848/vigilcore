<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetUserPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:password 
                            {email? : Adresse e-mail du collaborateur} 
                            {password? : Le nouveau mot de passe}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Modifie instantanément le mot de passe d\'un utilisateur existant';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("🔑  VIGILCORE — MODIFICATION DIRECTE DE MOT DE PASSE");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        $email = $this->argument('email');
        if (!$email) {
            $email = $this->ask('📧 Adresse e-mail du compte à modifier');
        }

        $email = strtolower(trim($email));
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ Aucun compte trouvé avec l'adresse [$email].");
            $this->comment("Pour créer ce compte, utilisez : php artisan user:create $email");
            return Command::FAILURE;
        }

        $password = $this->argument('password');
        if (!$password) {
            $password = $this->secret("Saisissez le nouveau mot de passe pour [{$user->name}] :");
            while (strlen($password) < 8) {
                $this->error('Le mot de passe doit comporter au moins 8 caractères.');
                $password = $this->secret("Saisissez le nouveau mot de passe pour [{$user->name}] :");
            }
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->newLine();
        $this->info("✅ MOT DE PASSE MIS À JOUR AVEC SUCCÈS !");
        $this->table(
            ['ID', 'Nom', 'Email', 'Statut'],
            [
                [$user->id, $user->name, $user->email, 'Mot de passe réinitialisé']
            ]
        );
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        return Command::SUCCESS;
    }
}
