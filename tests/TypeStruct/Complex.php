<?php

namespace Amsify42\Tests\TypeStruct;

use Amsify42\Tests\TypeStruct\Simple;

export typestruct Complex {
	uid: int,
	title: string<checkTitle>,
	price: numeric,
	is_test: tinyInt,
	simples: Simple[]
}