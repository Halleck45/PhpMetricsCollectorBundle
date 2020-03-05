<?php

namespace My\Controller;

use Symfony\Component\HttpFoundation\Response;

final class MyController
{
    public function route1(): Response
    {
        return new Response('<html><body>Hello world</body></html>', 200);
    }
}
