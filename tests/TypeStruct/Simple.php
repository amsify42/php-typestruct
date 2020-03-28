<?php

namespace Amsify42\Tests\TypeStruct;

export typestruct Simple {
	id: int,
	name: string<checkName>,
	email: string<email>,
	price: float
}