<?php

namespace Amsify42\Tests\TypeStruct;

export typestruct Simple {
	id: int,
	name: string<nonEmpty>,
	email: string<email>,
	price: float
}