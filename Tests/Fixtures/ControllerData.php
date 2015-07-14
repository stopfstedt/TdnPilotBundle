<?php

namespace Tdn\PilotBundle\Tests\Fixtures;

/**
 * Static data fixture for controller tests.
 *
 * Class ControllerData
 * @package Tdn\PilotBundle\Tests\Fixtures
 */
class ControllerData
{
    const BASIC_FOO_CONTROLLER = <<<'BASIC_CONTROLLER'
<?php

namespace Foo\BarBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Foo\BarBundle\Exception\InvalidFormException;
use Foo\BarBundle\Handler\FooHandler;
use Foo\BarBundle\Entity\FooInterface;

/**
 * Class FooController
 * @package Foo\BarBundle\Controller
 * @RouteResource("Foos")
 */
class FooController extends FOSRestController
{
    /**
     * Get a Foo
     *
     * @ApiDoc(
     *   section = "Foo",
     *   description = "Get a Foo.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Foo identifier."
     *     }
     *   },
     *   output="Foo\BarBundle\Entity\Foo",
     *   statusCodes={
     *     200 = "Foo.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Response
     */
    public function getAction($id)
    {
        $answer['foos'][] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all Foo.
     *
     * @ApiDoc(
     *   section = "Foo",
     *   description = "Get all Foo.",
     *   resource = true,
     *   output="Foo\BarBundle\Entity\Foo",
     *   statusCodes = {
     *     200 = "List of all Foo",
     *     204 = "No content. Nothing to list."
     *   }
     * )
     *
     * @QueryParam(
     *   name="offset",
     *   requirements="\d+",
     *   nullable=true,
     *   description="Offset from which to start listing notes."
     * )
     * @QueryParam(
     *   name="limit",
     *   requirements="\d+",
     *   default="20",
     *   description="How many notes to return."
     * )
     * @QueryParam(
     *   name="order_by",
     *   nullable=true,
     *   array=true,
     *   description="Order by fields. Must be an array ie. &order_by[name]=ASC&order_by[description]=DESC"
     * )
     * @QueryParam(
     *   name="filters",
     *   nullable=true,
     *   array=true,
     *   description="Filter by fields. Must be an array ie. &filters[id]=3"
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('order_by');
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : [];
        $criteria = array_map(function ($item) {
            $item = $item == 'null' ? null : $item;
            $item = $item == 'false' ? false : $item;
            $item = $item == 'true' ? true : $item;

            return $item;
        }, $criteria);

        $result = $this->getFooHandler()
            ->findFoosBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['foos'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a Foo.
     *
     * @ApiDoc(
     *   section = "Foo",
     *   description = "Create a Foo.",
     *   resource = true,
     *   input="Foo\BarBundle\Form\Type\FooType",
     *   output="Foo\BarBundle\Entity\Foo",
     *   statusCodes={
     *     201 = "Created Foo.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(statusCode=201, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postAction(Request $request)
    {
        try {
            $foo = $this->getFooHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_foos',
                    ['id' => $foo->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Foo.
     *
     * @ApiDoc(
     *   section = "Foo",
     *   description = "Update a Foo entity.",
     *   resource = true,
     *   input="Foo\BarBundle\Form\Type\FooType",
     *   output="Foo\BarBundle\Entity\Foo",
     *   statusCodes={
     *     200 = "Updated Foo.",
     *     201 = "Created Foo.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function putAction(Request $request, $id)
    {
        try {
            $foo = $this->getFooHandler()
                ->findFooBy(['id'=> $id]);
            if ($foo) {
                $code = Codes::HTTP_OK;
            } else {
                $foo = $this->getFooHandler()
                    ->createFoo();
                $code = Codes::HTTP_CREATED;
            }

            $answer['foo'] =
                $this->getFooHandler()->put(
                    $foo,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a Foo.
     *
     * @ApiDoc(
     *   section = "Foo",
     *   description = "Partial Update to a Foo.",
     *   resource = true,
     *   input="Foo\BarBundle\Form\Type\FooType",
     *   output="Foo\BarBundle\Entity\Foo",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="Foo identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated Foo.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function patchAction(Request $request, $id)
    {
        $answer['foo'] =
            $this->getFooHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a Foo.
     *
     * @ApiDoc(
     *   section = "Foo",
     *   description = "Delete a Foo entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "Foo identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Foo.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal FooInterface $foo
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $foo = $this->getOr404($id);

        try {
            $this->getFooHandler()
                ->deleteFoo($foo);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return FooInterface $foo
     */
    protected function getOr404($id)
    {
        $foo = $this->getFooHandler()
            ->findFooBy(['id' => $id]);
        if (!$foo) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $foo;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('foo');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return FooHandler
     */
    protected function getFooHandler()
    {
        return $this->container->get('foobar.foo.handler');
    }
}

BASIC_CONTROLLER;

}
