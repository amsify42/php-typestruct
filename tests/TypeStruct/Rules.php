<?php

namespace Amsify42\Tests\TypeStruct;

export typestruct Simple {
	email: string<email>,
	url: string<url.checkHost>,
	date: string<date>
}