<?php

declare(strict_types=1);

namespace CodeIgniter\Shield\Authentication;

use CodeIgniter\I18n\Time;
use CodeIgniter\Shield\Authentication\JWT\JWSEncoder;
use CodeIgniter\Shield\Entities\User;

/**
 * JWT Manager
 */
class JWTManager
{
    private Time $clock;
    private JWSEncoder $jwsEncoder;

    public function __construct(
        ?Time $clock = null,
        ?JWSEncoder $jwsEncoder = null
    ) {
        $this->clock      = $clock ?? new Time();
        $this->jwsEncoder = $jwsEncoder ?? new JWSEncoder($this->clock);
    }

    /**
     * Issues Signed JWT (JWS) for a User
     *
     * @param array                      $claims  The payload items.
     * @param int|null                   $ttl     Time to live in seconds.
     * @param string                     $keyset  The key group.
     *                                            The array key of Config\AuthJWT::$keys.
     * @param array<string, string>|null $headers An array with header elements to attach.
     */
    public function generateAccessToken(
        User $user,
        array $claims = [],
        ?int $ttl = null,
        $keyset = 'default',
        ?array $headers = null
    ): string {
        $payload = array_merge(
            $claims,
            [
                'sub' => (string) $user->id, // subject
            ],
        );

        return $this->generate($payload, $ttl, $keyset, $headers);
    }

    /**
     * Issues Signed JWT (JWS)
     *
     * @param array                      $claims  The payload items.
     * @param int|null                   $ttl     Time to live in seconds.
     * @param string                     $keyset  The key group.
     *                                            The array key of Config\AuthJWT::$keys.
     * @param array<string, string>|null $headers An array with header elements to attach.
     */
    public function generate(
        array $claims,
        ?int $ttl = null,
        $keyset = 'default',
        ?array $headers = null
    ): string {
        return $this->jwsEncoder->encode($claims, $ttl, $keyset, $headers);
    }
}
