<?php


namespace App\DataFixtures;

use App\Entity\user;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends AbstractFixtures
{

    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    protected function loadData(): void
    {
        //administrateur
        $this->createMany(3, 'user_admin', function(int $i){
            $admin = (new User());
            $hash = $this->encoder->encodePassword($admin, 'admin' . $i);
            $admin
                ->setEmail(sprintf('admin%d@kritik.fr', $i))
                ->setRoles(['ROLE_ADMIN'])
                ->setPassword($hash)
                ->setPseudo('admin' . $i);

            return $admin;
        });

        //user
        $this->createMany(10, 'user_user', function (int $i){
            $user = (new User());
            $hash = $this->encoder->encodePassword($user, 'user' . $i);
            $user
                ->setEmail(sprintf('user%d@mail.org', $i))
                ->setPassword($hash)
                ->setPseudo('user' . $i);

            return $user;
        });

    }
}